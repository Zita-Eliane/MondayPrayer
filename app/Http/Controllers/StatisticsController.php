<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{

    public function index(Request $request)
    {
        $year = (int) ($request->get('year') ?? now()->year);

        $leaders = Person::query()
            ->where('is_tracked', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $pivotTable = 'fast_leaders'; // ✅ chez toi

        // JEÛNES par dirigeant / mois
        $fastsAgg = DB::table($pivotTable)
            ->join('fasts', 'fasts.id', '=', $pivotTable . '.fast_id')
            ->selectRaw($pivotTable . '.person_id as person_id, EXTRACT(MONTH FROM fasts.fast_date)::int as month, COUNT(*)::int as fast_count')
            ->whereYear('fasts.fast_date', $year)
            ->groupBy('person_id', 'month')
            ->get();

        // PRIÈRE (minutes) par dirigeant / mois
        $prayerAgg = DB::table($pivotTable)
            ->join('fasts', 'fasts.id', '=', $pivotTable . '.fast_id')
            ->selectRaw($pivotTable . '.person_id as person_id, EXTRACT(MONTH FROM fasts.fast_date)::int as month, COALESCE(SUM(fasts.prayer_minutes),0)::int as total_minutes')
            ->whereYear('fasts.fast_date', $year)
            ->groupBy('person_id', 'month')
            ->get();

        $fastsMap = [];
        foreach ($fastsAgg as $row) {
            $fastsMap[$row->person_id][$row->month] = (int) $row->fast_count;
        }

        $prayerMap = [];
        foreach ($prayerAgg as $row) {
            $prayerMap[$row->person_id][$row->month] = (int) $row->total_minutes;
        }

        $rows = [];
        $totalsByMonthFasts = array_fill(1, 12, 0);
        $totalsByMonthPrayer = array_fill(1, 12, 0);

        $totalYearFasts = 0;
        $totalYearPrayer = 0;

        foreach ($leaders as $leader) {
            $months = [];

            $leaderYearFasts = 0;
            $leaderYearPrayer = 0;

            for ($m = 1; $m <= 12; $m++) {
                $cFasts = $fastsMap[$leader->id][$m] ?? 0;
                $cPrayer = $prayerMap[$leader->id][$m] ?? 0;

                $months[$m] = [
                    'fasts' => $cFasts,
                    'prayer_minutes' => $cPrayer,
                ];

                $leaderYearFasts += $cFasts;
                $leaderYearPrayer += $cPrayer;

                $totalsByMonthFasts[$m] += $cFasts;
                $totalsByMonthPrayer[$m] += $cPrayer;
            }

            $totalYearFasts += $leaderYearFasts;
            $totalYearPrayer += $leaderYearPrayer;

            $rows[] = [
                'leader_name' => $leader->name,
                'year_fasts' => $leaderYearFasts,
                'year_prayer_minutes' => $leaderYearPrayer,
                'months' => $months,
            ];
        }

        $years = \App\Models\Fast::selectRaw('EXTRACT(YEAR FROM fast_date)::int as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
           $years = [now()->year];
        }

        return view('statistics.index', compact(
            'year',
            'years',
            'rows',
            'totalsByMonthFasts',
            'totalsByMonthPrayer',
            'totalYearFasts',
            'totalYearPrayer'
        ));
    }
}
