<section>
    <form method="post" action="{{ route('profile.fasting.update') }}" style="display:flex;flex-direction:column;gap:1.25rem">
        @csrf
        @method('patch')

        {{-- Jour de jeûne --}}
        <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:8px">
                📅 Jour de jeûne hebdomadaire
            </label>
            <select name="fasting_day"
                style="width:100%;background:#0D1333;border:1px solid rgba(201,168,76,0.2);border-radius:10px;color:#E8EDF8;font-size:14px;padding:10px 14px;outline:none;transition:border-color 0.2s"
                onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='rgba(201,168,76,0.2)'">
                <option value="" style="background:#0D1333">— Aucun jour défini —</option>
                @php
                    $days = [0=>'Dimanche',1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi'];
                @endphp
                @foreach($days as $value => $label)
                    <option value="{{ $value }}" style="background:#0D1333"
                        {{ old('fasting_day', $user->fasting_day) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <p style="font-size:11px;color:var(--text-muted);margin-top:6px">
                Si tu n'as pas jeûné à l'heure du rappel, tu recevras une notification automatique.
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('fasting_day')" />
        </div>

        {{-- Heure du rappel --}}
        <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:8px">
                ⏰ Heure du rappel
            </label>
            <input type="time" name="fasting_reminder_time"
                style="width:100%;background:#0D1333;border:1px solid rgba(201,168,76,0.2);border-radius:10px;color:#E8EDF8;font-size:14px;padding:10px 14px;outline:none;transition:border-color 0.2s"
                onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='rgba(201,168,76,0.2)'"
                value="{{ old('fasting_reminder_time', $user->fasting_reminder_time ?? '20:30') }}">
            <x-input-error class="mt-2" :messages="$errors->get('fasting_reminder_time')" />
        </div>

        {{-- Notifications --}}
        <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:rgba(201,168,76,0.05);border:1px solid rgba(201,168,76,0.1);border-radius:10px">
            <input type="checkbox" id="notifications_enabled" name="notifications_enabled"
                style="width:18px;height:18px;cursor:pointer;accent-color:#C9A84C;flex-shrink:0"
                {{ old('notifications_enabled', $user->notifications_enabled) ? 'checked' : '' }}>
            <label for="notifications_enabled" style="font-size:13px;color:#E8EDF8;cursor:pointer;line-height:1.4">
                Recevoir les rappels par email
                <span style="display:block;font-size:11px;color:var(--text-muted);margin-top:2px">Un email sera envoyé si tu n'as pas jeûné ce jour-là</span>
            </label>
        </div>

        {{-- Submit --}}
        <div style="display:flex;align-items:center;gap:12px;padding-top:4px">
            <button type="submit" class="app-btn-primary">
                <svg style="width:15px;height:15px" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Enregistrer les préférences
            </button>

            @if(session('status') === 'fasting-prefs-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   style="font-size:13px;color:#34D399">
                    ✅ Préférences sauvegardées.
                </p>
            @endif
        </div>

    </form>
</section>