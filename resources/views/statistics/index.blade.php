<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h2 class="app-title">Statistiques</h2>
                <p style="color:var(--text-muted);font-size:13px;margin-top:4px">Vue communautaire — année {{ $year }}</p>
            </div>
            <form method="GET" action="{{ route('statistics.index') }}" style="display:flex;align-items:center;gap:8px">
                <label style="color:var(--text-muted);font-size:13px">Année</label>
                <select name="year" onchange="this.form.submit()"
                    style="background:#0D1333;border:1px solid rgba(201,168,76,0.25);border-radius:8px;color:#E8EDF8;font-size:13px;padding:6px 12px;outline:none;cursor:pointer">
                    @foreach($years as $y)
                        <option value="{{ $y }}" style="background:#0D1333" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    @php
        $monthNames = [1=>'Jan',2=>'Fév',3=>'Mar',4=>'Avr',5=>'Mai',6=>'Juin',7=>'Juil',8=>'Août',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Déc'];
        $fmt = function(int $seconds) {
            $seconds = max(0, $seconds);
            $h = intdiv($seconds, 3600);
            $m = intdiv($seconds % 3600, 60);
            $s = $seconds % 60;
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        };
        $totalPrayerSeconds = (int)$totalYearPrayer * 60;
        $topLeader = collect($rows)->sortByDesc('year_fasts')->first();
    @endphp

    <div style="max-width:1400px;margin:0 auto;padding:2rem 1.5rem">

        {{-- KPI Cards --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem">

            <div class="app-card" style="padding:1.5rem">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:12px">🗓️ Jeûnes (année)</div>
                <div style="font-size:36px;font-weight:700;color:#C9A84C;font-family:'Cinzel',serif;line-height:1">{{ (int)$totalYearFasts }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:8px">sessions enregistrées</div>
            </div>

            <div class="app-card" style="padding:1.5rem">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:12px">🙏 Temps prière (année)</div>
                <div style="font-size:28px;font-weight:700;color:#4F8EF7;font-family:'Cinzel',serif;line-height:1">{{ $fmt($totalPrayerSeconds) }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:8px">cumulé tous dirigeants</div>
            </div>

            <div class="app-card" style="padding:1.5rem">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:12px">👥 Dirigeants actifs</div>
                <div style="font-size:36px;font-weight:700;color:#34D399;font-family:'Cinzel',serif;line-height:1">
                    {{ collect($rows)->filter(fn($r) => $r['year_fasts'] > 0)->count() }}
                </div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:8px">sur {{ count($rows) }} suivis</div>
            </div>

            <div class="app-card" style="padding:1.5rem;border-color:rgba(201,168,76,0.2)">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:12px">🏆 Top dirigeant</div>
                <div style="font-size:20px;font-weight:700;color:#C9A84C;line-height:1.2">{{ $topLeader ? $topLeader['leader_name'] : '—' }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:8px">{{ $topLeader ? (int)$topLeader['year_fasts'].' jeûnes' : 'aucun' }}</div>
            </div>

        </div>

        {{-- Tableau --}}
        <div class="app-card" style="overflow:hidden">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <div class="section-title" style="margin-bottom:0">Nombre de jeûnes par dirigeant</div>
                <span class="badge badge-gold">{{ $year }}</span>
            </div>

            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <thead>
                        <tr style="background:#0D1333">
                            {{-- Dirigeant --}}
                            <th style="text-align:left;padding:10px 16px;color:#C9A84C;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:2px solid rgba(201,168,76,0.2);position:sticky;left:0;background:#0D1333;z-index:5;min-width:140px">
                                Dirigeant
                            </th>
                            {{-- Total Jeûnes --}}
                            <th style="text-align:center;padding:10px 12px;color:#C9A84C;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:2px solid rgba(201,168,76,0.2);min-width:90px;border-right:1px solid rgba(201,168,76,0.15)">
                                Total<br>Jeûnes
                            </th>
                            {{-- Temps Prière --}}
                            <th style="text-align:center;padding:10px 12px;color:#4F8EF7;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:2px solid rgba(201,168,76,0.2);min-width:100px;border-right:2px solid rgba(99,132,255,0.2)">
                                Temps<br>Prière
                            </th>
                            {{-- Mois --}}
                            @for($m=1; $m<=12; $m++)
                                <th style="text-align:center;padding:10px 8px;color:#8B9CC4;font-weight:600;font-size:11px;text-transform:uppercase;border-bottom:2px solid rgba(201,168,76,0.2);min-width:52px">
                                    {{ $monthNames[$m] }}
                                </th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($rows as $r)
                            @php
                                $active = (int)$r['year_fasts'] > 0;
                                $prayerSecs = (int)$r['year_prayer_minutes'] * 60;
                            @endphp
                            <tr style="border-bottom:1px solid rgba(99,132,255,0.08);{{ $active ? 'background:rgba(201,168,76,0.02)' : '' }}">

                                {{-- Nom --}}
                                <td style="padding:12px 16px;font-weight:{{ $active ? 600 : 400 }};color:{{ $active ? '#E8EDF8' : '#4a5580' }};position:sticky;left:0;background:{{ $active ? '#0e1530' : '#090D22' }};z-index:4;border-right:1px solid rgba(99,132,255,0.08)">
                                    @if($active)
                                        <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#C9A84C;margin-right:8px;vertical-align:middle"></span>
                                    @endif
                                    {{ $r['leader_name'] }}
                                </td>

                                {{-- Total Jeûnes --}}
                                <td style="text-align:center;padding:12px 8px;font-weight:700;font-size:15px;color:{{ $active ? '#C9A84C' : '#4a5580' }};border-right:1px solid rgba(201,168,76,0.15)">
                                    {{ (int)$r['year_fasts'] }}
                                </td>

                                {{-- Temps Prière --}}
                                <td style="text-align:center;padding:12px 8px;font-size:12px;font-weight:{{ $prayerSecs > 0 ? 600 : 400 }};color:{{ $prayerSecs > 0 ? '#4F8EF7' : '#4a5580' }};border-right:2px solid rgba(99,132,255,0.2)">
                                    {{ $prayerSecs > 0 ? $fmt($prayerSecs) : '—' }}
                                </td>

                                {{-- Mois --}}
                                @for($m=1; $m<=12; $m++)
                                    @php $v = (int)$r['months'][$m]['fasts']; @endphp
                                    <td style="text-align:center;padding:12px 8px">
                                        @if($v > 0)
                                            <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:rgba(201,168,76,0.15);color:#C9A84C;font-size:13px;font-weight:700">{{ $v }}</span>
                                        @else
                                            <span style="color:rgba(139,156,196,0.2);font-size:16px">·</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach

                        {{-- Ligne Total --}}
                        <tr style="border-top:2px solid rgba(201,168,76,0.25);background:rgba(201,168,76,0.04)">
                            <td style="padding:14px 16px;font-weight:700;color:#C9A84C;position:sticky;left:0;background:#0f1625;z-index:4;border-right:1px solid rgba(99,132,255,0.08)">
                                Total
                            </td>
                            <td style="text-align:center;padding:14px 8px;font-weight:700;font-size:15px;color:#C9A84C;border-right:1px solid rgba(201,168,76,0.15)">
                                {{ (int)$totalYearFasts }}
                            </td>
                            <td style="text-align:center;padding:14px 8px;font-size:12px;font-weight:700;color:#4F8EF7;border-right:2px solid rgba(99,132,255,0.2)">
                                {{ $fmt($totalPrayerSeconds) }}
                            </td>
                            @for($m=1; $m<=12; $m++)
                                @php $v = (int)$totalsByMonthFasts[$m]; @endphp
                                <td style="text-align:center;padding:14px 8px;font-weight:700;color:{{ $v > 0 ? '#C9A84C' : '#4a5580' }}">
                                    {{ $v ?: '0' }}
                                </td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>