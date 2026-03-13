<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl app-title leading-tight">Commencer une prière</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="app-card p-6" x-data="{ mode: 'subject' }">

                <form method="POST" action="{{ route('prayers.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-1 text-white/80">Date</label>
                        <input type="date" name="prayer_date" class="app-input"
                            value="{{ old('prayer_date', now()->toDateString()) }}" required>
                        @error('prayer_date') <p class="text-red-300 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>


                    <div>
                        <label class="block text-sm font-medium mb-1 text-white/80">Dirigeant</label>
                        <select name="leader_id" class="app-select" required>
                            <option value="">— Choisir —</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                            @endforeach
                        </select>
                        @error('leader_id') <p class="text-red-300 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-white/80">Type</label>
                        <div class="flex gap-4 text-white/80">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="mode" value="subject" checked x-model="mode">
                                Sujet
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="mode" value="proclamation" x-model="mode">
                                Proclamation
                            </label>
                        </div>
                        @error('mode') <p class="text-red-300 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-white/80">
                            Sujet / Proclamation (facultatif)
                        </label>
                        <textarea name="content" class="app-input" rows="3"
                                  placeholder="Ex: guérison, protection, faveur… ou une proclamation…">{{ old('content') }}</textarea>
                        @error('content') <p class="text-red-300 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('prayers.index') }}" class="btn-ghost">Annuler</a>
                        <button type="submit" class="btn-primary">Démarrer le timer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
