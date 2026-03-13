<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl app-title leading-tight">Prières</h2>
            <a href="{{ route('prayers.create') }}" class="btn-primary">+ Commencer</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 app-card px-4 py-2">{{ session('success') }}</div>
            @endif

            <div class="app-card">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm app-table">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-3">Date</th>
                                <th class="text-left px-4 py-3">Dirigeant</th>
                                <th class="text-left px-4 py-3">Mode</th>
                                <th class="text-left px-4 py-3">Sujet / Proclamation</th>
                                <th class="text-left px-4 py-3">Durée</th>
                                <th class="text-left px-4 py-3">Compteur</th>
                                <th class="text-right px-4 py-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($sessions as $s)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $s->prayer_date ? $s->prayer_date->format('d/m/Y') : '—' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $s->leader?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $s->mode === 'proclamation' ? 'Proclamation' : 'Sujet' }}
                                    </td>

                                    <td class="px-4 py-3 text-white/80">
                                        {{ $s->content ?: '—' }}
                                    </td>

                                   <td class="px-4 py-3">
                                        @if(!is_null($s->duration_seconds))
                                            @php
                                                $sec = (int) $s->duration_seconds;
                                                $h = intdiv($sec, 3600);
                                                $m = intdiv($sec % 3600, 60);
                                                $ss = $sec % 60;

                                                $dayKey = $s->prayer_date ? $s->prayer_date->format('Y-m-d') : null;
                                                $dayTotal = $dayKey && isset($dailyTotals[$dayKey]) ? (int) $dailyTotals[$dayKey]->total_seconds : null;
                                            @endphp

                                            <div>{{ sprintf('%02d:%02d:%02d', $h, $m, $ss) }}</div>

                                            @if($dayTotal)
                                                @php
                                                    $h2 = intdiv($dayTotal, 3600);
                                                    $m2 = intdiv($dayTotal % 3600, 60);
                                                    $s2 = $dayTotal % 60;
                                                @endphp
                                                <div class="text-xs text-white/60 mt-1">
                                                    Total jour : {{ sprintf('%02d:%02d:%02d', $h2, $m2, $s2) }}
                                                </div>
                                            @endif

                                        @elseif($s->started_at && !$s->ended_at)
                                            <a class="text-[var(--app-primary)] hover:underline"
                                            href="{{ route('prayers.show', $s) }}">
                                                En cours…
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>


                                    <td class="px-4 py-3">
                                        {{ $s->mode === 'proclamation' ? ($s->proclamation_count ?? 0) : '—' }}
                                    </td>

                                    {{-- ✅ Actions en dernier --}}
                                    <td class="px-4 py-3 text-right space-x-3">
                                        <a class="text-[var(--app-primary)] hover:underline"
                                        href="{{ route('prayers.edit', $s) }}">
                                            Modifier
                                        </a>

                                        <form class="inline" method="POST" action="{{ route('prayers.destroy', $s) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-400 hover:underline"
                                                    onclick="return confirm('Supprimer cette prière ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-white/70">
                                        Aucune prière enregistrée. Clique sur <b>+ Commencer</b>.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
