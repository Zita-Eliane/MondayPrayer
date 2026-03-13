<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl app-title leading-tight">Administration</h2>
                <p class="text-white/70 text-sm mt-1">Gestion de la communauté Koinonia</p>
            </div>
            <form method="POST" action="{{ route('admin.bulk-reminders') }}">
                @csrf
                <button type="submit"
                    onclick="return confirm('Envoyer un rappel à tous les membres qui n\'ont pas jeûné aujourd\'hui ?')"
                    class="app-btn-primary text-sm flex items-center gap-2">
                    <span>🔔</span> Rappeler les non-jeûneurs
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Messages flash --}}
            @if(session('success'))
                <div class="app-card p-4 border-l-4 border-green-400 text-green-300">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="app-card p-4 border-l-4 border-red-400 text-red-300">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- KPI Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $kpis = [
                        ['label' => 'Membres', 'value' => $stats['total_users'], 'icon' => '👥'],
                        ['label' => 'Total jeûnes', 'value' => $stats['total_fasts'], 'icon' => '🕊️'],
                        ['label' => 'Jeûnes aujourd\'hui', 'value' => $stats['fasts_today'], 'icon' => '📅'],
                        ['label' => 'Notifs actives', 'value' => $stats['active_users'], 'icon' => '🔔'],
                    ];
                @endphp

                @foreach($kpis as $kpi)
                    <div class="app-card p-5 text-center">
                        <div class="text-3xl mb-2">{{ $kpi['icon'] }}</div>
                        <div class="text-2xl font-bold text-white">{{ $kpi['value'] }}</div>
                        <div class="text-sm text-white/60 mt-1">{{ $kpi['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Tableau des membres --}}
            <div class="app-card p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Membres de la communauté</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm app-table">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-3">Nom</th>
                                <th class="text-left px-4 py-3">Email</th>
                                <th class="text-left px-4 py-3">Jour de jeûne</th>
                                <th class="text-left px-4 py-3">Rôle</th>
                                <th class="text-left px-4 py-3">Notifs</th>
                                <th class="text-left px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dayNames = [0=>'Dim', 1=>'Lun', 2=>'Mar', 3=>'Mer', 4=>'Jeu', 5=>'Ven', 6=>'Sam'];
                            @endphp
                            @foreach($users as $user)
                                <tr class="border-t border-white/10">
                                    <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-white/70">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        {{ $user->fasting_day !== null ? $dayNames[$user->fasting_day] : '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                            @csrf @method('PATCH')
                                            <select name="role"
                                                onchange="this.form.submit()"
                                                class="app-input py-1 text-xs"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Membre</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="{{ $user->notifications_enabled ? 'text-green-400' : 'text-white/40' }}">
                                            {{ $user->notifications_enabled ? '✅ Oui' : '⛔ Non' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.remind', $user) }}">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs text-blue-300 hover:text-blue-200 underline">
                                                Envoyer rappel
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
