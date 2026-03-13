<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fast;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Person;

class FastController extends Controller
{
    public function index(Request $request)
    {
        $query = Fast::with(['participant', 'leaders'])
            ->where('participant_user_id', Auth::id());

        $filterMonth = $request->get('month');
        $filterYear  = $request->get('year', now()->year);

        $query->whereYear('fast_date', $filterYear);
        if ($filterMonth) {
            $query->whereMonth('fast_date', $filterMonth);
        }

        // Admin voit tous les jeûnes de tous les participants
        if (Auth::user()->isAdmin()) {
            $query = Fast::with(['participant', 'leaders']);
            $query->whereYear('fast_date', $filterYear);
            if ($filterMonth) {
                $query->whereMonth('fast_date', $filterMonth);
            }
        }

        $fasts = $query->orderBy('fast_date', 'asc')->get();

        $years = Fast::when(!Auth::user()->isAdmin(), fn($q) => $q->where('participant_user_id', Auth::id()))
            ->selectRaw('EXTRACT(YEAR FROM fast_date)::int as year')
            ->distinct()->orderByDesc('year')->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        $prayerTotalsByDate = \App\Models\PrayerSession::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('duration_seconds')
            ->selectRaw('prayer_date, SUM(duration_seconds) as total_seconds')
            ->groupBy('prayer_date')
            ->get()
            ->keyBy(fn ($row) => \Carbon\Carbon::parse($row->prayer_date)->format('Y-m-d'));

        return view('fasts.index', compact('fasts', 'prayerTotalsByDate', 'filterMonth', 'filterYear', 'years'));
    }

    public function create()
    {
        $leaders = Person::orderBy('name')->get();

        $fastTypes = [
            'partial' => 'Jeûne Partiel',
            'total'   => 'Jeûne Total',
            'other'   => 'Autre',
        ];

        // Pour les admins : liste des participants
        $participants = Auth::user()->isAdmin()
            ? User::orderBy('name')->get(['id', 'name'])
            : collect();

        return view('fasts.create', compact('leaders', 'fastTypes', 'participants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fast_date'      => ['required', 'date'],
            'leader_ids'     => ['nullable', 'array'],
            'leader_ids.*'   => ['integer', 'exists:people,id'],
            'fast_type'      => ['required', 'in:partial,total,other'],
            'prayer_minutes' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'participant_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        // Admin peut choisir le participant, sinon c'est l'utilisateur connecté
        $participantId = (Auth::user()->isAdmin() && isset($data['participant_id']))
            ? $data['participant_id']
            : Auth::id();

        $prayerMinutes = $data['prayer_minutes'] ?? null;
        if ($prayerMinutes !== null && (int) $prayerMinutes === 0) {
            $prayerMinutes = null;
        }

        $fast = Fast::create([
            'participant_user_id' => $participantId,
            'fast_date'           => $data['fast_date'],
            'fast_type'           => $data['fast_type'],
            'prayer_minutes'      => $prayerMinutes,
        ]);

        $fast->leaders()->sync($data['leader_ids'] ?? []);

        return redirect()->route('fasts.index')->with('success', 'Jeûne enregistré ✅');
    }

    public function edit(Fast $fast)
    {
        // Admin peut modifier tous les jeûnes
        if (!Auth::user()->isAdmin()) {
            abort_unless($fast->participant_user_id === Auth::id(), 403);
        }

        $leaders = Person::orderBy('name')->get();
        $fastTypes = [
            'partial' => 'Jeûne Partiel',
            'total'   => 'Jeûne Total',
            'other'   => 'Autre',
        ];

        $participants = Auth::user()->isAdmin()
            ? User::orderBy('name')->get(['id', 'name'])
            : collect();

        return view('fasts.edit', compact('fast', 'leaders', 'fastTypes', 'participants'));
    }

    public function update(Request $request, Fast $fast)
    {
        if (!Auth::user()->isAdmin()) {
            abort_unless($fast->participant_user_id === Auth::id(), 403);
        }

        $data = $request->validate([
            'fast_date'      => ['required', 'date'],
            'leader_ids'     => ['nullable', 'array'],
            'leader_ids.*'   => ['integer', 'exists:people,id'],
            'fast_type'      => ['required', 'in:partial,total,other'],
            'prayer_minutes' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'participant_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $participantId = (Auth::user()->isAdmin() && isset($data['participant_id']))
            ? $data['participant_id']
            : $fast->participant_user_id;

        $fast->update([
            'participant_user_id' => $participantId,
            'fast_date'           => $data['fast_date'],
            'fast_type'           => $data['fast_type'],
            'prayer_minutes'      => (isset($data['prayer_minutes']) && (int)$data['prayer_minutes'] === 0)
                ? null
                : ($data['prayer_minutes'] ?? null),
        ]);

        $fast->leaders()->sync($data['leader_ids'] ?? []);

        return redirect()->route('fasts.index')->with('success', 'Jeûne mis à jour ✅');
    }

    public function destroy(Fast $fast)
    {
        if (!Auth::user()->isAdmin()) {
            abort_unless($fast->participant_user_id === Auth::id(), 403);
        }
        $fast->delete();
        return redirect()->route('fasts.index')->with('success', 'Jeûne supprimé 🗑️');
    }

    // Suppression multiple
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('fasts.index')->with('error', 'Aucun jeûne sélectionné.');
        }

        $query = Fast::whereIn('id', $ids);

        // Non-admin ne peut supprimer que ses propres jeûnes
        if (!Auth::user()->isAdmin()) {
            $query->where('participant_user_id', Auth::id());
        }

        $count = $query->count();
        $query->delete();

        return redirect()->route('fasts.index')->with('success', "{$count} jeûne(s) supprimé(s) 🗑️");
    }
}