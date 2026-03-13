<x-app-layout>

    @php
        $citations = [
            ["texte" => "L'Éternel est ma force et mon bouclier ; en lui mon cœur se confie.", "ref" => "Psaume 28:7"],
            ["texte" => "Cherchez d'abord le royaume de Dieu et sa justice, et toutes ces choses vous seront données par-dessus.", "ref" => "Matthieu 6:33"],
            ["texte" => "Je puis tout par celui qui me fortifie.", "ref" => "Philippiens 4:13"],
            ["texte" => "Invoke-moi, et je te répondrai ; je t'annoncerai de grandes choses.", "ref" => "Jérémie 33:3"],
            ["texte" => "L'Éternel est proche de tous ceux qui l'invoquent, de tous ceux qui l'invoquent avec sincérité.", "ref" => "Psaume 145:18"],
            ["texte" => "Celui qui demeure sous l'abri du Très-Haut repose à l'ombre du Tout-Puissant.", "ref" => "Psaume 91:1"],
            ["texte" => "Ne crains rien, car je suis avec toi ; ne promène pas des regards inquiets, car je suis ton Dieu.", "ref" => "Ésaïe 41:10"],
        ];
        $citation = $citations[now()->dayOfYear % count($citations)];

        $joursSemaine = [0=>'Dimanche',1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi'];
        $fastingDay = Auth::user()->fasting_day;
        $prochainJeune = null;
        if (!is_null($fastingDay)) {
            $today = now();
            $diff = ($fastingDay - $today->dayOfWeek + 7) % 7;
            $prochainJeune = $diff === 0 ? "Aujourd'hui !" : "Dans {$diff} jour(s) — " . $joursSemaine[$fastingDay];
        }
    @endphp

    <div style="max-width:1280px;margin:0 auto;padding:2rem 1.5rem">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:12px">
            <div>
                <h1 class="app-title" style="font-size:24px">Bonjour, {{ Auth::user()->name }} 👋</h1>
                <p style="color:var(--text-muted);font-size:13px;margin-top:4px">
                    {{ now()->translatedFormat('l d F Y') }} — Vue d'ensemble de tes prières
                </p>
            </div>
            <a href="{{ route('prayers.create') }}" class="app-btn-primary">
                <svg style="width:15px;height:15px" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                Commencer une prière
            </a>
        </div>

        {{-- Citation du jour --}}
        <div class="app-card" style="padding:1.25rem 1.5rem;margin-bottom:1.5rem;border-color:rgba(201,168,76,0.2);background:linear-gradient(135deg,rgba(201,168,76,0.06),rgba(79,142,247,0.04));display:flex;align-items:center;gap:16px">
            <div style="font-size:32px;flex-shrink:0">✨</div>
            <div>
                <p style="color:#E8C96A;font-style:italic;font-size:14px;line-height:1.6;margin-bottom:4px">"{{ $citation['texte'] }}"</p>
                <p style="color:var(--text-muted);font-size:12px">— {{ $citation['ref'] }}</p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem">

            {{-- Prochain jeûne --}}
            <div class="app-card" style="padding:1.25rem;border-color:rgba(201,168,76,0.2)">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:10px">🗓️ Prochain jeûne</div>
                @if($prochainJeune)
                    <div style="font-size:15px;font-weight:700;color:#C9A84C;line-height:1.3">{{ $prochainJeune }}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px">{{ $joursSemaine[$fastingDay] ?? '' }}</div>
                @else
                    <div style="font-size:13px;color:var(--text-muted)">Non défini</div>
                    <a href="{{ route('profile.edit') }}" style="font-size:11px;color:#4F8EF7;margin-top:6px;display:block">Configurer →</a>
                @endif
            </div>

            {{-- Temps total mois --}}
            <div class="app-card" style="padding:1.25rem">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:10px">🙏 Prière (mois)</div>
                <div style="font-size:22px;font-weight:700;color:#4F8EF7;font-family:'Cinzel',serif">{{ gmdate('H:i:s', (int)$totalSecondsMonth) }}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Sessions terminées</div>
            </div>

            {{-- 7 jours --}}
            <div class="app-card" style="padding:1.25rem">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:10px">📈 Prière (7 jours)</div>
                <div style="font-size:22px;font-weight:700;color:#4F8EF7;font-family:'Cinzel',serif">{{ gmdate('H:i:s', (int)$totalSeconds7) }}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:6px">7 derniers jours</div>
            </div>

            {{-- Sessions --}}
            <div class="app-card" style="padding:1.25rem">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:10px">🔢 Sessions (mois)</div>
                <div style="font-size:32px;font-weight:700;color:#34D399;font-family:'Cinzel',serif;line-height:1">{{ (int)$sessionsMonth }}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Toutes sessions</div>
            </div>

            {{-- Top dirigeant --}}
            <div class="app-card" style="padding:1.25rem;border-color:rgba(201,168,76,0.15)">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:10px">🏆 Top dirigeant</div>
                <div style="font-size:16px;font-weight:700;color:#C9A84C;line-height:1.3">{{ $topLeaderName ?? '—' }}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Ce mois-ci</div>
            </div>

        </div>

        {{-- Graphique + Top dirigeants --}}
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem">

            {{-- Graphique courbe --}}
            <div class="app-card" style="padding:1.5rem">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem">
                    <div class="section-title" style="margin-bottom:0">Temps de prière — 14 derniers jours</div>
                </div>
                <canvas id="lineDaily" height="100"></canvas>
                <p style="font-size:11px;color:var(--text-muted);margin-top:12px">Les valeurs représentent les minutes de prière par jour.</p>
            </div>

            {{-- Top dirigeants --}}
            <div class="app-card" style="padding:1.5rem">
                <div class="section-title">Top dirigeants (mois)</div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @forelse($topLeaders as $i => $row)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:rgba(255,255,255,0.04);border-radius:8px;border:1px solid rgba(99,132,255,0.08)">
                            <div style="display:flex;align-items:center;gap:10px">
                                <span style="width:22px;height:22px;border-radius:50%;background:{{ $i === 0 ? 'rgba(201,168,76,0.2)' : 'rgba(99,132,255,0.1)' }};color:{{ $i === 0 ? '#C9A84C' : '#8B9CC4' }};font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ $i+1 }}</span>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#E8EDF8">{{ $row->leader_name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted)">{{ (int)$row->sessions_count }} session(s)</div>
                                </div>
                            </div>
                            <div style="font-size:12px;font-weight:600;color:#4F8EF7">{{ gmdate('H:i:s', (int)$row->total_seconds) }}</div>
                        </div>
                    @empty
                        <p style="color:var(--text-muted);font-size:13px">Aucune donnée ce mois.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Mes derniers jeûnes --}}
        <div class="app-card" style="overflow:hidden">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <div class="section-title" style="margin-bottom:0">Mes derniers jeûnes</div>
                <a href="{{ route('fasts.index') }}" style="font-size:12px;color:#4F8EF7;text-decoration:none">Voir tout →</a>
            </div>
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Dirigeant(s)</th>
                        <th>Type</th>
                        <th>Temps prière</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentFasts ?? [] as $fast)
                        <tr>
                            <td class="font-medium">{{ \Carbon\Carbon::parse($fast->fast_date)->format('d/m/Y') }}</td>
                            <td>
                                @if($fast->leaders->count())
                                    <div style="display:flex;flex-wrap:wrap;gap:4px">
                                        @foreach($fast->leaders as $l)
                                            <span class="badge badge-gold">{{ $l->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                @if($fast->fast_type === 'partial') <span class="badge badge-blue">Partiel</span>
                                @elseif($fast->fast_type === 'total') <span class="badge badge-green">Total</span>
                                @else <span class="badge">Autre</span>
                                @endif
                            </td>
                            <td>
                                @if($fast->prayer_minutes)
                                    <span style="color:var(--gold)">🙏 {{ $fast->prayer_minutes }} min</span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted)">
                                Aucun jeûne enregistré. <a href="{{ route('fasts.create') }}" style="color:#C9A84C">+ Ajouter</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels14  = @json($labels14);
        const seconds14 = @json($seconds14);
        const minutes14 = seconds14.map(s => Math.round(s / 60));

        new Chart(document.getElementById('lineDaily'), {
            type: 'line',
            data: {
                labels: labels14,
                datasets: [{
                    label: 'Minutes',
                    data: minutes14,
                    borderColor: '#C9A84C',
                    backgroundColor: 'rgba(201,168,76,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#C9A84C',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => `${ctx.parsed.y} min` } }
                },
                scales: {
                    x: { ticks: { color: '#8B9CC4', font: { size: 11 } }, grid: { color: 'rgba(99,132,255,0.06)' } },
                    y: { ticks: { color: '#8B9CC4', font: { size: 11 } }, grid: { color: 'rgba(99,132,255,0.06)' }, beginAtZero: true },
                }
            }
        });
    </script>

</x-app-layout>