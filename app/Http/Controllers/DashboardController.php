<?php

namespace App\Http\Controllers;

use App\Models\PrayerSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Périodes
        $today = Carbon::today();
        $start7 = (clone $today)->subDays(6);
        $start14 = (clone $today)->subDays(13);
        $startMonth = Carbon::now()->startOfMonth();

        // Totaux (prière terminée uniquement)
        $totalSecondsMonth = (int) PrayerSession::where('user_id', $userId)
            ->whereNotNull('duration_seconds')
            ->whereDate('prayer_date', '>=', $startMonth)
            ->sum('duration_seconds');

        $totalSeconds7 = (int) PrayerSession::where('user_id', $userId)
            ->whereNotNull('duration_seconds')
            ->whereDate('prayer_date', '>=', $start7)
            ->sum('duration_seconds');

        $sessionsMonth = (int) PrayerSession::where('user_id', $userId)
            ->whereDate('prayer_date', '>=', $startMonth)
            ->count();

        $proclamationsMonth = (int) PrayerSession::where('user_id', $userId)
            ->whereDate('prayer_date', '>=', $startMonth)
            ->sum(DB::raw('COALESCE(proclamation_count, 0)'));

        // Bar chart: 14 derniers jours (total secondes par jour)
        $daily = PrayerSession::where('user_id', $userId)
            ->whereNotNull('duration_seconds')
            ->whereDate('prayer_date', '>=', $start14)
            ->selectRaw('prayer_date, SUM(duration_seconds) as total_seconds')
            ->groupBy('prayer_date')
            ->orderBy('prayer_date')
            ->get()
            ->keyBy(fn ($r) => Carbon::parse($r->prayer_date)->format('Y-m-d'));

        $labels14 = [];
        $seconds14 = [];
        for ($d = 13; $d >= 0; $d--) {
            $day = Carbon::today()->subDays($d);
            $key = $day->format('Y-m-d');
            $labels14[] = $day->format('d/m');
            $seconds14[] = isset($daily[$key]) ? (int) $daily[$key]->total_seconds : 0;
        }

        // Donut: mode month
        $modeCounts = PrayerSession::where('user_id', $userId)
            ->whereDate('prayer_date', '>=', $startMonth)
            ->selectRaw("mode, COUNT(*) as c")
            ->groupBy('mode')
            ->pluck('c', 'mode');

        $subjectCount = (int) ($modeCounts['subject'] ?? 0);
        $proclamationCount = (int) ($modeCounts['proclamation'] ?? 0);

        // Top dirigeants (mois) par total secondes
        $topLeaders = PrayerSession::where('user_id', $userId)
            ->whereNotNull('duration_seconds')
            ->whereDate('prayer_date', '>=', $startMonth)
            ->join('people', 'people.id', '=', 'prayer_sessions.leader_id')
            ->selectRaw('people.name as leader_name, SUM(prayer_sessions.duration_seconds) as total_seconds, COUNT(*) as sessions_count')
            ->groupBy('people.name')
            ->orderByDesc('total_seconds')
            ->limit(5)
            ->get();

        $topLeaderName = $topLeaders->first()->leader_name ?? '—';

        $recentFasts = \App\Models\Fast::with('leaders')
            ->where('participant_user_id', Auth::id())
            ->orderByDesc('fast_date')
            ->limit(5)
            ->get();


        return view('dashboard', [
            'totalSecondsMonth' => $totalSecondsMonth,
            'totalSeconds7' => $totalSeconds7,
            'sessionsMonth' => $sessionsMonth,
            'proclamationsMonth' => $proclamationsMonth,
            'topLeaderName' => $topLeaderName,
            'labels14' => $labels14,
            'seconds14' => $seconds14,
            'subjectCount' => $subjectCount,
            'proclamationCount' => $proclamationCount,
            'topLeaders' => $topLeaders,
        //    'labels14' => $labels14,
        //    'seconds14' => $seconds14,
        //    'subjectCount' => (int) $subjectCount,
        //    'proclamationCount' => (int) $proclamationCount,
            'recentFasts'         => $recentFasts,
        ]);
    }
}
