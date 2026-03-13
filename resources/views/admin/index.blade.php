<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <div>
                <h2 class="app-title">Administration</h2>
                <p style="color:var(--text-muted);font-size:13px;margin-top:4px">Gestion de la communauté Koinonia</p>
            </div>
            <form method="POST" action="{{ route('admin.bulk-reminders') }}">
                @csrf
                <button type="submit"
                    onclick="return confirm('Envoyer un rappel à tous les membres qui n\'ont pas jeûné aujourd\'hui ?')"
                    class="app-btn-primary">
                    <svg style="width:15px;height:15px" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                    Rappeler les non-jeûneurs
                </button>
            </form>
        </div>
    </x-slot>

    <div class="page-content fade-in">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:1.5rem">
                <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom:1.5rem">{{ session('error') }}</div>
        @endif

        {{-- KPI Cards --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem">

            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-blue">
                    <svg style="width:20px;height:20px;color:var(--blue-bright)" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                </div>
                <div class="kpi-value">{{ $stats['total_users'] }}</div>
                <div class="kpi-label">Membres</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-gold">
                    <svg style="width:20px;height:20px;color:var(--gold)" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                </div>
                <div class="kpi-value">{{ $stats['total_fasts'] }}</div>
                <div class="kpi-label">Total jeûnes</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-gold">
                    <svg style="width:20px;height:20px;color:var(--gold)" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                </div>
                <div class="kpi-value">{{ $stats['fasts_today'] }}</div>
                <div class="kpi-label">Jeûnes aujourd'hui</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-blue">
                    <svg style="width:20px;height:20px;color:var(--blue-bright)" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                </div>
                <div class="kpi-value">{{ $stats['active_users'] }}</div>
                <div class="kpi-label">Notifs actives</div>
            </div>

        </div>

        {{-- Tableau membres --}}
        <div class="app-card" style="overflow:hidden">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <div class="section-title" style="margin-bottom:0">Membres de la communauté</div>
                <span class="badge badge-blue">{{ count($users) }} membres</span>
            </div>

            <div style="overflow-x:auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Email</th>
                            <th>Jour de jeûne</th>
                            <th>Heure rappel</th>
                            <th>Rôle</th>
                            <th>Notifications</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dayNames = [0=>'Dimanche', 1=>'Lundi', 2=>'Mardi', 3=>'Mercredi', 4=>'Jeudi', 5=>'Vendredi', 6=>'Samedi'];
                        @endphp
                        @foreach($users as $user)
                            <tr>
                                <td class="font-medium">
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="avatar" style="width:32px;height:32px;font-size:11px">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->fasting_day !== null)
                                        <span class="badge badge-gold">{{ $dayNames[$user->fasting_day] }}</span>
                                    @else
                                        <span style="color:var(--text-muted)">—</span>
                                    @endif
                                </td>
                                <td>{{ $user->fasting_reminder_time ?? '20:30' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                        @csrf @method('PATCH')
                                        <select name="role" onchange="this.form.submit()"
                                            style="width:auto;padding:4px 10px;font-size:12px"
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Membre</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if($user->notifications_enabled)
                                        <span class="badge badge-green">Actif</span>
                                    @else
                                        <span class="badge badge-red">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.remind', $user) }}">
                                        @csrf
                                        <button type="submit" class="app-btn-secondary" style="padding:5px 12px;font-size:12px">
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
</x-app-layout>
