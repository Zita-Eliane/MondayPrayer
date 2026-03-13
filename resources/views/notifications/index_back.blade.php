<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl app-title leading-tight">Notifications</h2>
                <p class="text-white/70 text-sm mt-1">
                    {{ auth()->user()->unreadNotifications->count() }} non lue(s)
                </p>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-sm text-blue-300 hover:text-blue-200 underline">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-3">

            @forelse($notifications as $notif)
                @php $data = $notif->data; @endphp
                <div class="app-card p-4 flex items-start gap-4 {{ $notif->read_at ? 'opacity-60' : '' }}">
                    <div class="text-2xl">{{ $data['icon'] ?? '🔔' }}</div>
                    <div class="flex-1">
                        <div class="font-semibold text-white">{{ $data['title'] ?? 'Notification' }}</div>
                        <div class="text-sm text-white/70 mt-0.5">{{ $data['message'] ?? '' }}</div>
                        <div class="text-xs text-white/40 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($data['url'] ?? false)
                            <a href="{{ $data['url'] }}" class="text-xs text-blue-300 hover:text-blue-200">
                                Voir →
                            </a>
                        @endif
                        @if(!$notif->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-white/40 hover:text-white/70">✓ Lu</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="app-card p-10 text-center text-white/50">
                    <div class="text-4xl mb-3">🕊️</div>
                    Aucune notification pour le moment.
                </div>
            @endforelse

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
