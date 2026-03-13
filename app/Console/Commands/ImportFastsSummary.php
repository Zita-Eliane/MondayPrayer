<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Models\Fast;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportFastsSummary extends Command
{
    protected $signature = 'koinonia:import-summary
                            {file : Chemin vers le fichier CSV de synthèse}
                            {year : Année des données (ex: 2026)}
                            {--user-id= : ID utilisateur à associer}
                            {--dry-run : Simulation sans écriture}
                            {--delimiter=, : Délimiteur CSV}';

    protected $description = 'Importe les jeûnes depuis un CSV de synthèse mensuelle (1 ligne par dirigeant)';

    // Noms des mois dans l'ordre (tels qu'ils apparaissent dans le CSV)
    private array $monthNames = [
        1  => ['janvier','jan'],
        2  => ['février','fevrier','fév','fev'],
        3  => ['mars'],
        4  => ['avril','avr'],
        5  => ['mai'],
        6  => ['juin'],
        7  => ['juillet','juil'],
        8  => ['août','aout'],
        9  => ['septembre','sep'],
        10 => ['octobre','oct'],
        11 => ['novembre','nov'],
        12 => ['décembre','decembre','déc','dec'],
    ];

    public function handle(): int
    {
        $file   = $this->argument('file');
        $year   = (int) $this->argument('year');
        $dryRun = $this->option('dry-run');
        $delim  = $this->option('delimiter');

        if (! file_exists($file)) {
            $this->error("Fichier introuvable : {$file}");
            return 1;
        }

        // Utilisateur
        $userId = $this->option('user-id');
        if (! $userId) {
            $user = User::where('role', 'admin')->first() ?? User::first();
            if (! $user) { $this->error('Aucun utilisateur trouvé.'); return 1; }
            $userId = $user->id;
        }

        $this->info("📥 Import synthèse {$year} — utilisateur ID {$userId}");
        $dryRun && $this->warn('⚠️  Mode DRY-RUN activé');

        // ── Lecture CSV ────────────────────────────────────────────────
        $handle  = fopen($file, 'r');
        $rawHeaders = fgetcsv($handle, 0, $delim);
        $headers = array_map(fn($h) => mb_strtolower(trim($h)), $rawHeaders);

        // Détecte la colonne "Dirigeants" et les colonnes mois
        $leaderCol  = array_search('dirigeants', $headers);
        if ($leaderCol === false) {
            // Essaie la première colonne
            $leaderCol = 0;
        }

        // Mappe les colonnes → numéros de mois
        $monthCols = [];
        foreach ($headers as $idx => $h) {
            foreach ($this->monthNames as $num => $variants) {
                if (in_array($h, $variants)) {
                    $monthCols[$num] = $idx;
                    break;
                }
            }
        }

        if (empty($monthCols)) {
            $this->error('Aucune colonne de mois détectée. Vérifie les en-têtes.');
            $this->line('En-têtes détectés : ' . implode(' | ', $headers));
            return 1;
        }

        $this->line('Mois détectés : ' . implode(', ', array_keys($monthCols)));

        // ── Traitement des lignes ──────────────────────────────────────
        $totalFasts   = 0;
        $totalSkipped = 0;

        while (($row = fgetcsv($handle, 0, $delim)) !== false) {
            $leaderName = trim($row[$leaderCol] ?? '');

            // Ignore lignes vides, "Total", notes
            if (empty($leaderName) || strtolower($leaderName) === 'total') continue;
            if (str_starts_with($leaderName, 'Se rassurer')) continue;

            $this->line("\n👤 <info>{$leaderName}</info>");

            // Trouve ou crée le Person
            $person = null;
            if (! $dryRun) {
                $person = Person::firstOrCreate(['name' => $leaderName]);
            }

            foreach ($monthCols as $month => $colIdx) {
                $count = (int) ($row[$colIdx] ?? 0);
                if ($count === 0) continue;

                // Génère $count dates distribuées dans le mois
                $dates = $this->distributeDates($year, $month, $count);

                $this->line("  📅 Mois {$month} → {$count} jeûne(s) sur : " . implode(', ', array_map(fn($d) => $d->format('d/m'), $dates)));

                if (! $dryRun) {
                    foreach ($dates as $date) {
                        // Vérifie si un jeûne existe déjà ce jour pour ce dirigeant
                        $exists = DB::table('fast_leaders')
                            ->join('fasts', 'fasts.id', '=', 'fast_leaders.fast_id')
                            ->where('fast_leaders.person_id', $person->id)
                            ->whereDate('fasts.fast_date', $date->format('Y-m-d'))
                            ->exists();

                        if ($exists) {
                            $this->line("    ⚠️  Déjà existant le {$date->format('d/m/Y')}, ignoré.");
                            $totalSkipped++;
                            continue;
                        }

                        $fast = Fast::create([
                            'fast_date'            => $date->format('Y-m-d'),
                            'fast_type'            => 'partial',
                            'prayer_minutes'       => null,
                            'participant_user_id'  => $userId,
                        ]);

                        DB::table('fast_leaders')->insert([
                            'fast_id'   => $fast->id,
                            'person_id' => $person->id,
                        ]);

                        $totalFasts++;
                    }
                } else {
                    $totalFasts += $count;
                }
            }
        }

        fclose($handle);

        // ── Résumé ─────────────────────────────────────────────────────
        $this->newLine();
        $this->table(
            ['Jeûnes créés', 'Doublons ignorés'],
            [[$totalFasts, $totalSkipped]]
        );

        if ($dryRun) {
            $this->warn('DRY-RUN — relance sans --dry-run pour enregistrer.');
        } else {
            $this->info("✅ Import terminé !");
        }

        return 0;
    }

    /**
     * Distribue $count dates dans le mois $year-$month.
     * Priorité : lundis du mois, puis autres jours si besoin.
     */
    private function distributeDates(int $year, int $month, int $count): array
    {
        $start = Carbon::create($year, $month, 1);
        $end   = $start->copy()->endOfMonth();

        // Collecte tous les lundis du mois
        $mondays = [];
        $day = $start->copy()->next(Carbon::MONDAY);
        if ($day->month === $month) {
            // Si le 1er est lundi, partir du 1er
            $firstDay = $start->copy();
            if ($firstDay->dayOfWeek === Carbon::MONDAY) {
                $day = $firstDay;
            }
        }

        $cur = $start->copy();
        while ($cur->lte($end)) {
            if ($cur->dayOfWeek === Carbon::MONDAY) {
                $mondays[] = $cur->copy();
            }
            $cur->addDay();
        }

        // Si le nombre dépasse les lundis, ajoute les jeudis
        $thursdays = [];
        $cur = $start->copy();
        while ($cur->lte($end)) {
            if ($cur->dayOfWeek === Carbon::THURSDAY) {
                $thursdays[] = $cur->copy();
            }
            $cur->addDay();
        }

        // Si encore pas assez, prend tous les jours de semaine
        $weekdays = [];
        $cur = $start->copy();
        while ($cur->lte($end)) {
            if (! $cur->isWeekend()) {
                $weekdays[] = $cur->copy();
            }
            $cur->addDay();
        }

        // Combine et déduplique
        $allDates = array_unique(
            array_merge($mondays, $thursdays, $weekdays),
            SORT_REGULAR
        );

        usort($allDates, fn($a, $b) => $a->timestamp - $b->timestamp);

        // Distribue uniformément
        if ($count >= count($allDates)) {
            return array_slice($allDates, 0, $count);
        }

        // Sélection uniforme des $count dates parmi celles disponibles
        $step    = count($allDates) / $count;
        $selected = [];
        for ($i = 0; $i < $count; $i++) {
            $idx = (int) round($i * $step);
            $idx = min($idx, count($allDates) - 1);
            $selected[] = $allDates[$idx];
        }

        return $selected;
    }
}
