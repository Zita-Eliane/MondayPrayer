<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="app-title">Mon profil</h2>
            <p style="color:var(--text-muted);font-size:13px;margin-top:4px">Gérer tes informations personnelles et préférences</p>
        </div>
    </x-slot>

    <div style="max-width:760px;margin:0 auto;padding:2rem 1.5rem;display:flex;flex-direction:column;gap:1.5rem">

        {{-- Informations personnelles --}}
        <div class="app-card" style="padding:2rem">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border)">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(79,142,247,0.15);border:1px solid rgba(79,142,247,0.3);display:flex;align-items:center;justify-content:center">
                    <svg style="width:18px;height:18px;color:#4F8EF7" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <div style="font-weight:600;color:#E8EDF8;font-size:14px">Informations personnelles</div>
                    <div style="font-size:12px;color:var(--text-muted)">Nom et adresse email</div>
                </div>
            </div>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Mot de passe --}}
        <div class="app-card" style="padding:2rem">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border)">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(52,211,153,0.15);border:1px solid rgba(52,211,153,0.3);display:flex;align-items:center;justify-content:center">
                    <svg style="width:18px;height:18px;color:#34D399" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <div style="font-weight:600;color:#E8EDF8;font-size:14px">Mot de passe</div>
                    <div style="font-size:12px;color:var(--text-muted)">Modifier ton mot de passe</div>
                </div>
            </div>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Préférences de jeûne --}}
        <div class="app-card" style="padding:2rem;border-color:rgba(201,168,76,0.2)">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid rgba(201,168,76,0.15)">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.3);display:flex;align-items:center;justify-content:center;font-size:18px">
                    🕊️
                </div>
                <div>
                    <div style="font-weight:600;color:#C9A84C;font-size:14px">Préférences de jeûne</div>
                    <div style="font-size:12px;color:var(--text-muted)">Jour de jeûne et rappels</div>
                </div>
            </div>
            @include('profile.partials.fasting-preferences-form')
        </div>

        {{-- Supprimer le compte --}}
        <div class="app-card" style="padding:2rem;border-color:rgba(239,68,68,0.15)">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid rgba(239,68,68,0.1)">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);display:flex;align-items:center;justify-content:center">
                    <svg style="width:18px;height:18px;color:#FCA5A5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <div style="font-weight:600;color:#FCA5A5;font-size:14px">Zone dangereuse</div>
                    <div style="font-size:12px;color:var(--text-muted)">Supprimer ton compte définitivement</div>
                </div>
            </div>
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>