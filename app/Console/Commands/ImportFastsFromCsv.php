<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Models\Fast;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportFastsFromCsv extends Command
{
    protected $signature   = 'koinonia:import-fasts {file : Chemin vers le fichier CSV}
                                {--user-id= : ID de l\'utilisateur à associer (défaut : premier admin)}
                                {--dry-run : Simulation sans écriture en base}
                                {--delimiter=, : Délimiteur CSV (défaut: virgule)}';

    protected $description = 'Importe les jeûnes depuis un fichier CSV';

    public function handle(): int
    {
        $file     = $this->argument('file');
        $dryRun   = $this->option('dry-run');
        $delim    = $this->option('delimiter');
        $userId   = $this->option('user-id');

        // ── Vérifications ─────────────────────────────────────────────
        if (! file_exists($file)) {
            $this->error("Fichier introuvable : {$file}");
            return 1;
        }

        // Utilisateur par défaut = premier admin (ou premier user)
        if (! $userId) {
            $user = User::where('role', 'admin')->first() ?? User::first();
            if (! $user) {
                $this->error('Aucun utilisateur trouvé. Précise --user-id=X');
                return 1;
            }
            $userId = $user->id;
        }

        $this->info("Utilisateur cible : ID {$userId}");
        $dryRun && $this->warn('⚠️  Mode DRY-RUN — aucune donnée ne sera enregistrée.');

        // ── Lecture CSV ────────────────────────────────────────────────
        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle, 0, $delim);

        // Normalise les en-têtes (minuscules, sans espaces)
        $headers = array_map(fn($h) => mb_strtolower(trim($h)), $headers);
        $this->line('Colonnes détectées : ' . implode(' | ', $headers));

        // Mapping flexible des colonnes
        $colMap = $this->detectColumns($headers);
        $this->line('Mapping colonnes : ' . json_encode($colMap));

        if (! isset($colMap['date'])) {
            $this->error('Colonne de date introuvable. Colonnes attendues : date_jeune, date, fast_date...');
            return 1;
        }

        // ── Import ─────────────────────────────────────────────────────
        $imported = 0;
        $skipped  = 0;
        $errors   = 0;
        $line     = 1;

        $bar = $this->output->createProgressBar();
        $bar->start();

        while (($row = fgetcsv($handle, 0, $delim)) !== false) {
            $line++;
            if (count($row) < 2) { $skipped++; continue; }

            $data = array_combine($headers, array_pad($row, count($headers), ''));

            try {
                $result = $this->processRow($data, $colMap, $userId, $dryRun);
                $result ? $imported++ : $skipped++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("Ligne {$line} ignorée : " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        fclose($handle);
        $this->newLine(2);

        // ── Résumé ─────────────────────────────────────────────────────
        $this->table(
            ['Importés', 'Ignorés', 'Erreurs'],
            [[$imported, $skipped, $errors]]
        );

        if ($dryRun) {
            $this->warn('DRY-RUN terminé. Relance sans --dry-run pour importer réellement.');
        } else {
            $this->info("✅ Import terminé — {$imported} jeûnes enregistrés.");
        }

        return 0;
    }

    // ─────────────────────────────────────────────────────────────────
    private function detectColumns(array $headers): array
    {
        $map = [];

        $candidates = [
            'date'     => ['date_jeune', 'date du jeûne', 'date', 'fast_date', 'date jeune', 'date_du_jeune'],
            'leader'   => ['dirigeant', 'leader', 'nom du dirigeant', 'dirigeant(s)', 'person'],
            'type'     => ['type de jeûne', 'type_jeune', 'type', 'fast_type', 'type jeune'],
            'prayer'   => ['temps de prière', 'temps_priere', 'prayer_minutes', 'priere', 'temps priere', 'durée prière'],
            'participant' => ['nom du participant', 'participant', 'user', 'nom'],
        ];

        foreach ($candidates as $key => $options) {
            foreach ($options as $option) {
                if (in_array($option, $headers)) {
                    $map[$key] = $option;
                    break;
                }
            }
        }

        return $map;
    }

    // ─────────────────────────────────────────────────────────────────
    private function processRow(array $data, array $colMap, int $userId, bool $dryRun): bool
    {
        // Date
        $rawDate = trim($data[$colMap['date']] ?? '');
        if (empty($rawDate)) return false;

        $date = $this->parseDate($rawDate);
        if (! $date) {
            throw new \RuntimeException("Date non reconnue : '{$rawDate}'");
        }

        // Type de jeûne
        $rawType  = mb_strtolower(trim($data[$colMap['type']] ?? 'partial'));
        $fastType = match(true) {
            str_contains($rawType, 'total')   => 'total',
            str_contains($rawType, 'partiel') => 'partial',
            str_contains($rawType, 'partial') => 'partial',
            default                            => 'partial',
        };

        // Temps de prière (en minutes)
        $rawPrayer     = trim($data[$colMap['prayer']] ?? '');
        $prayerMinutes = $this->parsePrayerMinutes($rawPrayer);

        // Dirigeant(s) — peut contenir plusieurs noms séparés par "/" ou ","
        $leaderIds = [];
        if (isset($colMap['leader'])) {
            $rawLeader = trim($data[$colMap['leader']] ?? '');
            if (! empty($rawLeader)) {
                $names = preg_split('/[\/,;]+/', $rawLeader);
                foreach ($names as $name) {
                    $name = trim($name);
                    if (empty($name)) continue;

                    if (! $dryRun) {
                        $person = Person::firstOrCreate(['name' => $name]);
                        $leaderIds[] = $person->id;
                    }
                }
            }
        }

        if ($dryRun) {
            $this->line(sprintf(
                '  [DRY] %s | %s | %s min | dirigeant: %s',
                $date->format('d/m/Y'),
                $fastType,
                $prayerMinutes ?? '—',
                $data[$colMap['leader']] ?? '—'
            ));
            return true;
        }

        // Création du jeûne
        $fast = Fast::create([
            'fast_date'      => $date->format('Y-m-d'),
            'fast_type'      => $fastType,
            'prayer_minutes' => $prayerMinutes,
            'user_id'        => $userId,
        ]);

        // Liaison dirigeants
        if (! empty($leaderIds)) {
            DB::table('fast_leaders')->insert(
                array_map(fn($lid) => [
                    'fast_id'   => $fast->id,
                    'person_id' => $lid,
                ], $leaderIds)
            );
        }

        return true;
    }

    // ─────────────────────────────────────────────────────────────────
    private function parseDate(string $raw): ?Carbon
    {
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'd/m/y'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $raw);
            } catch (\Throwable) {}
        }
        return null;
    }

    private function parsePrayerMinutes(string $raw): ?int
    {
        if (empty($raw) || in_array(mb_strtolower($raw), ['', '-', 'non évalué', 'non evalue', 'n/a', 'na'])) {
            return null;
        }

        // "1h30" → 90min, "30min" → 30, "1h" → 60
        if (preg_match('/(\d+)h(?:(\d+))?/i', $raw, $m)) {
            return (int)$m[1] * 60 + (int)($m[2] ?? 0);
        }

        // Nombre brut (minutes)
        if (is_numeric($raw)) {
            return (int) $raw;
        }

        return null;
    }
}