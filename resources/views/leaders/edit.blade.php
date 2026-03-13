<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl app-title leading-tight">Modifier un dirigeant</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="app-card p-6">
                <form method="POST" action="{{ route('leaders.update', $leader) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-1 text-white/80">Nom</label>
                        <input name="name" class="app-input" value="{{ old('name', $leader->name) }}" required>
                        @error('name')
                            <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('leaders.index') }}" class="btn-ghost">Annuler</a>
                        <button type="submit" class="btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
