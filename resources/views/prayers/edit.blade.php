<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl app-title leading-tight">
            Modifier la prière
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="app-card p-6">

                <form method="POST" action="{{ route('prayers.update', $prayer) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Dirigeant --}}
                    <div>
                        <label class="block text-sm mb-1">Dirigeant</label>
                        <select name="leader_id" class="app-input w-full" required>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}"
                                    @selected($leader->id === $prayer->leader_id)>
                                    {{ $leader->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm mb-1">Date de prière</label>
                        <input type="date"
                               name="prayer_date"
                               class="app-input w-full"
                               value="{{ $prayer->prayer_date?->format('Y-m-d') }}"
                               required>
                    </div>

                    {{-- Mode --}}
                    <div>
                        <label class="block text-sm mb-1">Mode</label>
                        <select name="mode" class="app-input w-full" required>
                            <option value="subject" @selected($prayer->mode === 'subject')>Sujet</option>
                            <option value="proclamation" @selected($prayer->mode === 'proclamation')>Proclamation</option>
                        </select>
                    </div>

                    {{-- Sujet / Proclamation --}}
                    <div>
                        <label class="block text-sm mb-1">Sujet / Proclamation</label>
                        <textarea name="content"
                                  rows="3"
                                  class="app-input w-full"
                                  placeholder="Facultatif">{{ old('content', $prayer->content) }}</textarea>
                    </div>

                    {{-- Durée (uniquement si terminée) --}}
                    @if($prayer->ended_at)
                        <div>
                            <label class="block text-sm mb-1">Durée (en secondes)</label>
                            <input type="number"
                                   name="duration_seconds"
                                   min="0"
                                   class="app-input w-full"
                                   value="{{ $prayer->duration_seconds }}">
                            <p class="text-xs text-white/60 mt-1">
                                Tu peux corriger la durée si nécessaire.
                            </p>
                        </div>
                    @endif

                    {{-- Compteur (proclamation seulement) --}}
                    @if($prayer->mode === 'proclamation')
                        <div>
                            <label class="block text-sm mb-1">Compteur</label>
                            <input type="number"
                                   name="proclamation_count"
                                   min="0"
                                   class="app-input w-full"
                                   value="{{ $prayer->proclamation_count ?? 0 }}">
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex justify-between pt-4">
                        <a href="{{ route('prayers.index') }}"
                           class="text-white/70 hover:underline">
                            Annuler
                        </a>

                        <button type="submit" class="btn-primary">
                            Enregistrer
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
