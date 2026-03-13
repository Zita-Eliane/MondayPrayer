<?php

namespace App\Http\Controllers;

use App\Models\PrayerSession;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PrayerSessionController extends Controller
{
   public function index()
    {
        $sessions = PrayerSession::with('leader')
            ->where('user_id', Auth::id())
            ->orderByDesc('prayer_date')
            ->orderByDesc('created_at')
            ->get();

        $dailyTotals = PrayerSession::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('duration_seconds')
            ->selectRaw('prayer_date, SUM(duration_seconds) as total_seconds')
            ->groupBy('prayer_date')
            ->orderByDesc('prayer_date')
            ->get()
            ->keyBy(function ($row) {
                return Carbon::parse($row->prayer_date)->format('Y-m-d');
            });

        return view('prayers.index', compact('sessions', 'dailyTotals'));
    }


    public function create()
    {
        $leaders = Person::orderBy('name')->get();

        return view('prayers.create', compact('leaders'));
    }

    // Démarrer une prière : crée une session avec started_at = now
    public function store(Request $request)
    {
        $data = $request->validate([
            'leader_id' => ['required', 'exists:people,id'],
            'mode' => ['required', 'in:subject,proclamation'],
            'content' => ['nullable', 'string', 'max:2000'],
            'prayer_date' => ['required', 'date'], // ✅
   
        ]);

        $session = PrayerSession::create([
            'user_id' => Auth::id(),
            'leader_id' => $data['leader_id'],
            'prayer_date' => $data['prayer_date'], // ✅
            'mode' => $data['mode'],
            'content' => $data['content'] ?? null,
            'started_at' => now(),
            'is_running' => true,
            'active_seconds' => 0,
            'proclamation_count' => $data['mode'] === 'proclamation' ? 0 : null,
        ]);

        return redirect()->route('prayers.show', $session);
    }

    // Page “session en cours”
    public function show(PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);
        $prayer->load('leader');

        return view('prayers.show', ['prayer' => $prayer]);
    }

    // Stop : calcule durée
    public function stop(PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);

        if (!$prayer->started_at || $prayer->ended_at) {
            return redirect()->route('prayers.index');
        }

        $now = now();

        // total cumulé (pause/reprise)
        $total = (int) ($prayer->active_seconds ?? 0);

        // Calcul robuste (timestamps)
        $start = $prayer->started_at instanceof \Carbon\CarbonInterface
            ? $prayer->started_at
            : \Carbon\Carbon::parse($prayer->started_at);

       
        $delta = max(0, $now->getTimestamp() - $start->getTimestamp());
        $total += $delta;
        

        // Option : si tu veux éviter 0 quand quelqu’un stop trop vite
        // $total = max(1, $total);

        $prayer->update([
            'ended_at' => $now,
            'duration_seconds' => $total,
            'active_seconds' => $total,
            'is_running' => false,
            'paused_at' => null,
        ]);

        return redirect()->route('prayers.index')->with('success', 'Prière enregistrée ✅');
    }




    // Compteur +/- (proclamation)
    public function updateCounter(Request $request, PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);
        abort_unless($prayer->mode === 'proclamation', 403);

        $data = $request->validate([
            'delta' => ['required', 'integer', 'in:-1,1'],
        ]);

        $current = (int) ($prayer->proclamation_count ?? 0);
        $new = max(0, $current + (int) $data['delta']);

        $prayer->update(['proclamation_count' => $new]);

        return response()->json(['count' => $new]);
    }

    public function pause(PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);
        if (!$prayer->started_at || $prayer->ended_at || !$prayer->is_running) {
            return redirect()->route('prayers.show', $prayer);
        }

        $now = now();
        $delta = max(0, (int) $now->diffInSeconds($prayer->started_at));

        $prayer->update([
            'active_seconds' => (int) $prayer->active_seconds + $delta,
            'paused_at' => $now,
            'is_running' => false,
        ]);

        return redirect()->route('prayers.show', $prayer)->with('success', 'Prière en pause ⏸️');
    }


    public function resume(PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);
        if ($prayer->ended_at || $prayer->is_running) {
            return redirect()->route('prayers.show', $prayer);
        }

        $prayer->update([
            'started_at' => now(),   // on redémarre une période active
            'paused_at' => null,
            'is_running' => true,
        ]);

        return redirect()->route('prayers.show', $prayer)->with('success', 'Prière reprise ▶️');
    }


    public function edit(PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);
        $leaders = Person::orderBy('name')->get();

        return view('prayers.edit', compact('prayer', 'leaders'));
    }

    public function update(Request $request, PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);

        $data = $request->validate([
            'leader_id' => ['required', 'exists:people,id'],
            'prayer_date' => ['required', 'date'],
            'mode' => ['required', 'in:subject,proclamation'],
            'content' => ['nullable', 'string', 'max:2000'],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'proclamation_count' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        // Si la prière est terminée, on autorise l’édition de la durée
        if ($prayer->ended_at) {
            if ($data['mode'] === 'subject') {
                $data['proclamation_count'] = null;
            } else {
                $data['proclamation_count'] = $data['proclamation_count'] ?? ($prayer->proclamation_count ?? 0);
            }

            $prayer->update([
                'leader_id' => $data['leader_id'],
                'prayer_date' => $data['prayer_date'],
                'mode' => $data['mode'],
                'content' => $data['content'] ?? null,
                'duration_seconds' => $data['duration_seconds'] ?? $prayer->duration_seconds,
                'active_seconds' => $data['duration_seconds'] ?? $prayer->active_seconds,
                'proclamation_count' => $data['mode'] === 'proclamation'
                    ? ($data['proclamation_count'] ?? $prayer->proclamation_count ?? 0)
                    : null,
            ]);
        } else {
            // session en cours : on modifie seulement meta (pas la durée)
            $prayer->update([
                'leader_id' => $data['leader_id'],
                'prayer_date' => $data['prayer_date'],
                'mode' => $data['mode'],
                'content' => $data['content'] ?? null,
                'proclamation_count' => $data['mode'] === 'proclamation'
                    ? ($prayer->proclamation_count ?? 0)
                    : null,
            ]);
        }

        return redirect()->route('prayers.index')->with('success', 'Prière mise à jour ✅');
    }


    public function destroy(PrayerSession $prayer)
    {
        abort_unless($prayer->user_id === Auth::id(), 403);
        $prayer->delete();

        return redirect()->route('prayers.index')->with('success', 'Prière supprimée 🗑️');
    }



}
