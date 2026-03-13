<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h2 class="app-title">Notifications</h2>
                <p style="color:var(--text-muted);font-size:13px;margin-top:4px">
                    {{ auth()->user()->unreadNotifications->count() }} non lue(s)
                </p>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="app-btn-secondary" style="font-size:13px">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="page-content fade-in" style="max-width:720px">

        @forelse($notifications as $notif)
            @php $data = $notif->data; @endphp
            <div class="app-card {{ $notif->read_at ? '' : 'app-card-gold' }}"
                 style="padding:1.25rem;margin-bottom:0.75rem;display:flex;align-items:flex-start;gap:1rem;opacity:{{ $notif->read_at ? '0.6' : '1' }}">

                {{-- Icône --}}
                <div class="kpi-icon kpi-icon-gold" style="width:44px;height:44px;flex-shrink:0;font-size:20px">
                    {{ $data['icon'] ?? '🔔' }}
                </div>

                {{-- Contenu --}}
                <div style="flex:1;min-width:0">
                    <div style="font-weight:600;color:var(--text-primary);margin-bottom:3px">
                        {{ $data['title'] ?? 'Notification' }}
                    </div>
                    <div style="font-size:13.5px;color:var(--text-secondary)">
                        {{ $data['message'] ?? '' }}
                    </div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:6px">
                        {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
                    @if($data['url'] ?? false)
                        <a href="{{ $data['url'] }}" class="app-btn-secondary" style="padding:5px 12px;font-size:12px">
                            Voir →
                        </a>
                    @endif
                    @if(!$notif->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" style="font-size:12px;color:var(--text-muted);background:none;border:none;cursor:pointer;padding:0">
                                Marquer lu
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @empty
            <div class="app-card" style="padding:4rem;text-align:center">
                <div style="font-size:48px;margin-bottom:1rem">🕊️</div>
                <div style="font-family:'Cinzel',serif;color:var(--gold);font-size:16px;margin-bottom:8px">Tout est tranquille</div>
                <div style="color:var(--text-muted);font-size:13.5px">Aucune notification pour le moment.</div>
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div style="margin-top:1.5rem">{{ $notifications->links() }}</div>
        @endif

    </div>
</x-app-layout>
