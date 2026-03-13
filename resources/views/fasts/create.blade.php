<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h2 class="app-title">Ajouter un jeûne</h2>
                <p style="color:var(--text-muted);font-size:13px;margin-top:4px">Enregistre ton jeûne du jour</p>
            </div>
            <a href="{{ route('fasts.index') }}" class="app-btn-secondary">← Retour</a>
        </div>
    </x-slot>

    <div class="page-content fade-in" style="max-width:680px">
        <div class="app-card app-card-gold" style="padding:2rem">
            <form method="POST" action="{{ route('fasts.store') }}">
                @csrf

                {{-- Participant (admin seulement) --}}
                @if(Auth::user()->isAdmin() && $participants->count())
                    <div style="margin-bottom:1.5rem;padding:1rem;background:rgba(201,168,76,0.06);border:1px solid rgba(201,168,76,0.15);border-radius:10px">
                        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#C9A84C;margin-bottom:8px">
                            👤 Participant (Admin)
                        </label>
                        <select name="participant_id" required
                            style="width:100%;background:#0D1333;border:1px solid rgba(201,168,76,0.2);border-radius:10px;color:#E8EDF8;font-size:14px;padding:10px 14px;outline:none">
                            <option value="">— Choisir le participant —</option>
                            @foreach($participants as $p)
                                <option value="{{ $p->id }}" style="background:#0D1333"
                                    {{ old('participant_id') == $p->id || $p->id === Auth::id() ? 'selected' : '' }}>
                                    {{ $p->name }}{{ $p->id === Auth::id() ? ' (moi)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p style="font-size:11px;color:var(--text-muted);margin-top:6px">
                            ⚡ Visible uniquement pour les administrateurs.
                        </p>
                    </div>
                @endif

                {{-- Date --}}
                <div style="margin-bottom:1.5rem">
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:8px">📅 Date du jeûne</label>
                    <input type="date" name="fast_date"
                        style="width:100%;background:#0D1333;border:1px solid rgba(99,132,255,0.15);border-radius:10px;color:#E8EDF8;font-size:14px;padding:10px 14px;outline:none;transition:border-color 0.2s"
                        onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='rgba(99,132,255,0.15)'"
                        value="{{ old('fast_date', date('Y-m-d')) }}" required>
                    @error('fast_date')<p style="color:#FCA5A5;font-size:12px;margin-top:6px">{{ $message }}</p>@enderror
                </div>

                {{-- Dirigeants --}}
                <div style="margin-bottom:1.5rem">
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:8px">👥 Dirigeant(s)</label>
                    <select name="leader_ids[]" multiple
                        style="width:100%;background:#0D1333;border:1px solid rgba(99,132,255,0.15);border-radius:10px;color:#E8EDF8;font-size:14px;padding:6px;height:160px;outline:none"
                        onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='rgba(99,132,255,0.15)'">
                        @foreach($leaders as $leader)
                            <option value="{{ $leader->id }}" style="padding:8px 10px;background:#0D1333;color:#E8EDF8"
                                {{ in_array($leader->id, old('leader_ids', [])) ? 'selected' : '' }}>
                                {{ $leader->name }}
                            </option>
                        @endforeach
                    </select>
                    <p style="font-size:11px;color:var(--text-muted);margin-top:6px">
                        💡 Maintiens <kbd style="background:#192460;border:1px solid rgba(99,132,255,0.2);border-radius:4px;padding:1px 5px;font-size:10px">Ctrl</kbd> pour sélectionner plusieurs.
                    </p>
                    @error('leader_ids')<p style="color:#FCA5A5;font-size:12px;margin-top:6px">{{ $message }}</p>@enderror
                </div>

                {{-- Type de jeûne --}}
                <div style="margin-bottom:1.5rem">
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:8px">🕊️ Type de jeûne</label>
                    <select name="fast_type" required
                        style="width:100%;background:#0D1333;border:1px solid rgba(99,132,255,0.15);border-radius:10px;color:#E8EDF8;font-size:14px;padding:10px 14px;outline:none;appearance:auto"
                        onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='rgba(99,132,255,0.15)'">
                        @foreach($fastTypes as $value => $label)
                            <option value="{{ $value }}" style="background:#0D1333;color:#E8EDF8"
                                {{ old('fast_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('fast_type')<p style="color:#FCA5A5;font-size:12px;margin-top:6px">{{ $message }}</p>@enderror
                </div>

                {{-- Temps de prière --}}
                <div style="margin-bottom:2rem">
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:8px">
                        🙏 Temps de prière <span style="font-weight:400;text-transform:none;letter-spacing:0">(en minutes, facultatif)</span>
                    </label>
                    <input type="number" name="prayer_minutes" min="0" max="1440" placeholder="Ex: 30"
                        style="width:100%;background:#0D1333;border:1px solid rgba(99,132,255,0.15);border-radius:10px;color:#E8EDF8;font-size:14px;padding:10px 14px;outline:none"
                        onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='rgba(99,132,255,0.15)'"
                        value="{{ old('prayer_minutes') }}">
                    <p style="font-size:11px;color:var(--text-muted);margin-top:6px">Laisse vide si tu ne veux pas renseigner.</p>
                    @error('prayer_minutes')<p style="color:#FCA5A5;font-size:12px;margin-top:6px">{{ $message }}</p>@enderror
                </div>

                <div style="height:1px;background:rgba(99,132,255,0.1);margin-bottom:1.5rem"></div>

                <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px">
                    <a href="{{ route('fasts.index') }}" class="app-btn-secondary">Annuler</a>
                    <button type="submit" class="app-btn-primary">
                        <svg style="width:15px;height:15px" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Enregistrer le jeûne
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>