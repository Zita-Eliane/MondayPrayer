<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl app-title leading-tight">Dirigeants</h2>
            <a href="{{ route('leaders.create') }}" class="btn-primary">+ Ajouter</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 app-card px-4 py-2">
                    {{ session('success') }}
                </div>
            @endif

            <div class="app-card">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm app-table">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-3">Nom</th>
                                <th class="text-right px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($leaders as $leader)
                            <tr>
                                <td class="px-4 py-3">{{ $leader->name }}</td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    <a class="text-[var(--app-primary)] hover:underline"
                                       href="{{ route('leaders.edit', $leader) }}">
                                        Modifier
                                    </a>

                                    <form class="inline" method="POST" action="{{ route('leaders.destroy', $leader) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-400 hover:underline"
                                                onclick="return confirm('Supprimer ce dirigeant ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-10 text-center text-white/70">
                                    Aucun dirigeant. Clique sur <b>+ Ajouter</b>.
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
