<style>
#koi-nav{background:#090D22;border-bottom:1px solid rgba(99,132,255,0.12);position:sticky;top:0;z-index:50;backdrop-filter:blur(20px)}
#koi-nav-inner{max-width:1280px;margin:0 auto;padding:0 20px;height:60px;display:flex;flex-direction:row;align-items:center;gap:8px}
.koi-logo{display:flex;flex-direction:row;align-items:center;gap:8px;text-decoration:none;flex-shrink:0;margin-right:8px}
.koi-logo-text{font-family:'Cinzel',serif;font-size:16px;font-weight:600;color:#C9A84C;letter-spacing:0.05em;white-space:nowrap}
.koi-link{display:inline-flex;flex-direction:row;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;font-size:13px;font-weight:500;color:#8B9CC4;text-decoration:none;white-space:nowrap;transition:all 0.2s;line-height:1}
.koi-link:hover{color:#E8EDF8;background:rgba(79,142,247,0.12)}
.koi-link.active{color:#C9A84C;background:rgba(201,168,76,0.1)}
.koi-link svg{width:15px;height:15px;flex-shrink:0}
.koi-theme-btn{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:8px;background:#111B42;border:1px solid rgba(99,132,255,0.15);color:#E8EDF8;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;margin-left:auto}
.koi-theme-btn:hover{border-color:rgba(201,168,76,0.3);color:#C9A84C}
.koi-theme-btn svg{width:14px;height:14px;flex-shrink:0}
.koi-user-btn{display:inline-flex;flex-direction:row;align-items:center;gap:8px;padding:5px 12px;border-radius:8px;background:#111B42;border:1px solid rgba(99,132,255,0.15);color:#E8EDF8;font-size:13px;font-weight:500;cursor:pointer;white-space:nowrap;margin-left:auto;flex-shrink:0}
.koi-user-btn:hover{border-color:rgba(201,168,76,0.3);color:#C9A84C}
.koi-avatar{width:28px;height:28px;border-radius:50%;background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.3);display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#C9A84C;flex-shrink:0;font-family:serif}
.koi-dropdown{position:absolute;right:16px;top:68px;min-width:210px;background:#111B42;border:1px solid rgba(201,168,76,0.2);border-radius:12px;padding:6px;box-shadow:0 8px 32px rgba(0,0,0,0.5);z-index:100}
.koi-drop-item{display:flex;flex-direction:row;align-items:center;gap:9px;padding:9px 12px;border-radius:7px;color:#8B9CC4;text-decoration:none;font-size:13px;font-weight:500;transition:all 0.15s;cursor:pointer;background:none;border:none;width:100%;text-align:left}
.koi-drop-item:hover{background:rgba(79,142,247,0.1);color:#E8EDF8}
.koi-drop-item svg{width:15px;height:15px;flex-shrink:0}
.koi-divider{height:1px;background:rgba(99,132,255,0.1);margin:4px 6px}
.koi-badge{background:#C9A84C;color:#1A1000;font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;margin-left:auto}

html.light #koi-nav{background:#F7F9FF;border-bottom:1px solid rgba(30,58,138,0.12)}
html.light .koi-logo-text{color:#8A6A1B}
html.light .koi-link{color:#38508A}
html.light .koi-link:hover{color:#10234D;background:rgba(47,107,255,0.08)}
html.light .koi-link.active{color:#8A6A1B;background:rgba(201,168,76,0.16)}
html.light .koi-theme-btn,
html.light .koi-user-btn{background:#FFFFFF;color:#1B2D57;border-color:rgba(56,80,138,0.22)}
html.light .koi-avatar{background:rgba(201,168,76,0.18);color:#7D5F1B;border-color:rgba(201,168,76,0.35)}
html.light .koi-dropdown{background:#FFFFFF;border-color:rgba(56,80,138,0.2)}
html.light .koi-drop-item{color:#38508A}
html.light .koi-drop-item:hover{background:rgba(47,107,255,0.08);color:#10234D}
html.light .koi-divider{background:rgba(56,80,138,0.16)}
</style>

<nav id="koi-nav">
    <div id="koi-nav-inner">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="koi-logo">
            <svg viewBox="0 0 40 48" fill="none" style="width:24px;height:30px;flex-shrink:0">
                <path d="M20 4C20 4 10 14 10 24C10 30 13 35 20 38C27 35 30 30 30 24C30 14 20 4 20 4Z" fill="#A8882E" opacity="0.9"/>
                <path d="M20 14C20 14 14 20 14 26C14 30 16.5 33 20 35C23.5 33 26 30 26 26C26 20 20 14 20 14Z" fill="#E8C96A"/>
                <path d="M20 22C20 22 17 26 17 28.5C17 30.5 18.3 32 20 32C21.7 32 23 30.5 23 28.5C23 26 20 22 20 22Z" fill="#FFFBE6"/>
            </svg>
            <span class="koi-logo-text">Sentinelle</span>
        </a>

        {{-- Liens --}}
        <a href="{{ route('dashboard') }}" class="koi-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
            Dashboard
        </a>

        <a href="{{ route('fasts.index') }}" class="koi-link {{ request()->routeIs('fasts.*') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
            Jeûnes
        </a>

        <a href="{{ route('leaders.index') }}" class="koi-link {{ request()->routeIs('leaders.*') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
            Dirigeants
        </a>

        <a href="{{ route('prayers.index') }}" class="koi-link {{ request()->routeIs('prayers.*') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
            Prières
        </a>

        <a href="{{ route('statistics.index') }}" class="koi-link {{ request()->routeIs('statistics.*') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
            Statistiques
        </a>

        <button id="koi-theme-toggle" type="button" class="koi-theme-btn" aria-label="Basculer le thème" title="Basculer le thème">
            <svg id="koi-theme-icon" viewBox="0 0 20 20" fill="currentColor"></svg>
            <span id="koi-theme-label">Mode sombre</span>
        </button>

        {{-- Bouton utilisateur --}}
        <button onclick="document.getElementById('koi-menu').classList.toggle('hidden')" class="koi-user-btn">
            <span class="koi-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
            {{ Auth::user()->name }}
            <svg style="width:12px;height:12px;opacity:0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>

    </div>
</nav>

{{-- Dropdown menu --}}
<div id="koi-menu" class="hidden koi-dropdown">

    <a href="{{ route('profile.edit') }}" class="koi-drop-item">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
        Mon profil
    </a>

    <a href="{{ route('notifications.index') }}" class="koi-drop-item">
        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
        Notifications
        @php $unread = Auth::user()->unreadNotifications->count() @endphp
        @if($unread > 0)
            <span class="koi-badge">{{ $unread }}</span>
        @endif
    </a>

    @if(Auth::user()->isAdmin())
        <div class="koi-divider"></div>
        <a href="{{ route('admin.index') }}" class="koi-drop-item" style="color:#C9A84C">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
            Administration
        </a>
    @endif

    <div class="koi-divider"></div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="koi-drop-item" style="color:#FCA5A5">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
            Déconnexion
        </button>
    </form>

</div>

{{-- Fermer le menu en cliquant ailleurs --}}
<script>
document.addEventListener('click', function(e) {
    const menu = document.getElementById('koi-menu');
    const btn = e.target.closest('.koi-user-btn');
    if (!btn && menu && !menu.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

(() => {
    const root = document.documentElement;
    const btn = document.getElementById('koi-theme-toggle');
    const icon = document.getElementById('koi-theme-icon');
    const label = document.getElementById('koi-theme-label');

    if (!btn || !icon || !label) return;

    const sunSvg = '<path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm0 13a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm8-5a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zm10.657-5.657a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM7.464 13.95a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zm8.193 1.414a1 1 0 01-1.414 0l-.707-.707a1 1 0 111.414-1.414l.707.707a1 1 0 010 1.414zM7.464 6.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 116.757 3.93l.707.707a1 1 0 010 1.414zM10 6a4 4 0 100 8 4 4 0 000-8z"/>';
    const moonSvg = '<path d="M17.293 13.293a8 8 0 01-10.586-10.586 1 1 0 00-1.35-1.35A10 10 0 1018.643 14.64a1 1 0 00-1.35-1.347z"/>';

    const applyThemeUi = () => {
        const isDark = root.classList.contains('dark');
        icon.innerHTML = isDark ? sunSvg : moonSvg;
        label.textContent = isDark ? 'Mode clair' : 'Mode sombre';
    };

    applyThemeUi();

    btn.addEventListener('click', () => {
        const isDark = root.classList.contains('dark');
        root.classList.toggle('dark', !isDark);
        root.classList.toggle('light', isDark);
        localStorage.setItem('theme', !isDark ? 'dark' : 'light');
        applyThemeUi();
    });
})();
</script>