<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Fast;
use App\Notifications\FastingReminderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Middleware admin sur toutes les méthodes
    
    // ── Dashboard admin ────────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total_users'   => User::count(),
            'total_fasts'   => Fast::count(),
            'fasts_today' => Fast::whereDate('fast_date', today())->count(),
            'active_users'  => User::where('notifications_enabled', true)->count(),
        ];

        $users = User::orderBy('name')->get();

        return view('admin.index', compact('stats', 'users'));
    }

    // ── Liste des membres ──────────────────────────────────────────
    public function users()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users', compact('users'));
    }

    // ── Modifier le rôle d'un utilisateur ─────────────────────────
    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,member']);

        // Protection : on ne peut pas retirer son propre rôle admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $user->update(['role' => $request->role]);
        return back()->with('success', "Rôle de {$user->name} mis à jour.");
    }

    // ── Envoyer un rappel manuel à un utilisateur ─────────────────
    public function sendManualReminder(User $user)
    {
        $user->notify(new FastingReminderNotification());
        return back()->with('success', "Rappel envoyé à {$user->name}.");
    }

    // ── Envoyer un rappel à tous ceux qui n'ont pas jeûné aujourd'hui
    public function sendBulkReminders()
    {
        $today     = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;

        $users = User::where('notifications_enabled', true)
                     ->where('fasting_day', $dayOfWeek)
                     ->whereDoesntHave('fasts', function ($q) use ($today) {
                         $q->whereDate('fast_date', $today);
                     })
                     ->get();

        foreach ($users as $user) {
            $user->notify(new FastingReminderNotification());
        }

        return back()->with('success', "{$users->count()} rappel(s) envoyé(s).");
    }

    // ── Statistiques globales ──────────────────────────────────────
    public function statistics()
    {
        $year = request('year', now()->year);

        // Jeûnes par mois
        $fastsByMonth = Fast::selectRaw("EXTRACT(MONTH FROM fast_date) as month, COUNT(*) as total")
            ->whereYear('fast_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Top jeûneurs
        $topFasters = User::withCount(['fasts' => function ($q) use ($year) {
                $q->whereYear('fast_date', $year);
            }])
            ->orderByDesc('fasts_count')
            ->limit(10)
            ->get();

        return view('admin.statistics', compact('fastsByMonth', 'topFasters', 'year'));
    }
}
