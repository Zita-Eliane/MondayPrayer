<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl app-title leading-tight">
                Prière — {{ $prayer->leader->name }}
            </h2>

            <div class="flex items-center gap-3">
                @if($prayer->is_running)
                <form method="POST" action="{{ route('prayers.pause', $prayer) }}">
                    @csrf
                    <button class="btn-ghost">Pause</button>
                </form>
                @else
                    @if(!$prayer->ended_at)
                        <form method="POST" action="{{ route('prayers.resume', $prayer) }}">
                            @csrf
                            <button class="btn-ghost">Reprendre</button>
                        </form>
                    @endif
                @endif

                <form method="POST" action="{{ route('prayers.stop', $prayer) }}">
                    @csrf
                    <button class="btn-primary">Stop & Enregistrer</button>
                </form>
            </div>

        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="app-card p-6"
                 x-data="prayerTimer({
                    startedAt: '{{ $prayer->started_at?->toIso8601String() }}',
                    activeSeconds: {{ (int)$prayer->active_seconds }},
                    isRunning: {{ $prayer->is_running ? 'true' : 'false' }},
                    isProclamation: {{ $prayer->mode === 'proclamation' ? 'true' : 'false' }},
                    sessionId: {{ $prayer->id }},
                    initialCount: {{ (int)($prayer->proclamation_count ?? 0) }},
                    csrf: '{{ csrf_token() }}',
                    counterUrl: '{{ route('prayers.counter', $prayer) }}'
                 })">

                <div class="text-white/80 mb-2">
                    <div><b>Mode :</b> {{ $prayer->mode === 'proclamation' ? 'Proclamation' : 'Sujet' }}</div>
                    <div class="mt-1"><b>Texte :</b> {{ $prayer->content ?: '—' }}</div>
                </div>

                <div class="mt-6">
                    <div class="text-white/60 text-sm">Temps écoulé</div>
                    <div class="text-4xl font-semibold text-white mt-1" x-text="display">00:00:00</div>
                </div>

                <template x-if="isProclamation">
                    <div class="mt-8">
                        <div class="text-white/60 text-sm mb-2">Compteur de proclamations</div>

                        <div class="flex items-center gap-3">
                            <button type="button" class="btn-ghost" @click="change(-1)">−</button>
                            <div class="text-3xl font-semibold text-[var(--app-gold)]" x-text="count">0</div>
                            <button type="button" class="btn-ghost" @click="change(1)">+</button>
                        </div>

                        <p class="text-white/60 text-xs mt-2">
                            Ce compteur s’enregistre automatiquement.
                        </p>
                    </div>
                </template>

            </div>
        </div>
    </div>

    <script>
        function prayerTimer({ startedAt, activeSeconds, isRunning, isProclamation, initialCount, csrf, counterUrl }) {
            return {
                isProclamation,
                count: initialCount,
                display: "00:00:00",
                startMs: startedAt ? Date.parse(startedAt) : null,
                activeSeconds,
                isRunning,

                init() {
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                },

                tick() {
                    let total = this.activeSeconds;

                    if (this.isRunning && this.startMs) {
                        total += Math.max(0, Math.floor((Date.now() - this.startMs) / 1000));
                    }

                    const h = String(Math.floor(total / 3600)).padStart(2, '0');
                    const m = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
                    const s = String(total % 60).padStart(2, '0');
                    this.display = `${h}:${m}:${s}`;
                },

                async change(delta) {
                    const res = await fetch(counterUrl, {
                        method: "POST",
                        headers: {"Content-Type":"application/json","X-CSRF-TOKEN": csrf,"Accept":"application/json"},
                        body: JSON.stringify({ delta })
                    });

                    if (!res.ok) return;
                    const data = await res.json();
                    this.count = data.count;
                }
            }
        }
    </script>

</x-app-layout>
