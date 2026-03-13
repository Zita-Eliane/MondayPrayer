<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h2 class="app-title">Jeûnes</h2>
                <p style="color:var(--text-muted);font-size:13px;margin-top:4px">Historique des jeûnes enregistrés</p>
            </div>
            <a href="{{ route('fasts.create') }}" class="app-btn-primary">
                <svg style="width:15px;height:15px" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                Ajouter un jeûne
            </a>
        </div>
    </x-slot>

    <div style="max-width:1280px;margin:0 auto;padding:2rem 1.5rem">

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:1.5rem">
                <svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulaire global (filtre + bulk delete) --}}
        <form method="GET" action="{{ route('fasts.index') }}" id="filter-form"
            style="display:flex;align-items:center;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap">
            <select name="year" style="background:#0D1333;border:1px solid rgba(201,168,76,0.25);border-radius:8px;color:#E8EDF8;font-size:13px;padding:7px 12px;outline:none">
                @foreach($years as $y)
                    <option value="{{ $y }}" style="background:#0D1333" {{ $y == $filterYear ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="month" style="background:#0D1333;border:1px solid rgba(99,132,255,0.2);border-radius:8px;color:#E8EDF8;font-size:13px;padding:7px 12px;outline:none">
                <option value="">Tous les mois</option>
                @foreach([1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'] as $n => $label)
                    <option value="{{ $n }}" style="background:#0D1333" {{ $n == $filterMonth ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="app-btn-primary" style="padding:7px 16px">Filtrer</button>
            @if($filterMonth)
                <a href="{{ route('fasts.index', ['year' => $filterYear]) }}" class="app-btn-secondary" style="padding:7px 16px">✕ Effacer</a>
            @endif
        </form>

        {{-- Formulaire bulk delete --}}
        <form method="POST" action="{{ route('fasts.bulk-destroy') }}" id="bulk-form">
            @csrf
            @method('DELETE')

            <div class="app-card" style="overflow:hidden">
                <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div class="section-title" style="margin-bottom:0">Liste des jeûnes</div>
                        <span class="badge badge-blue">{{ count($fasts) }} entrées</span>
                    </div>

                    {{-- Bouton suppression multiple (caché par défaut) --}}
                    <button type="submit" id="bulk-delete-btn"
                        onclick="return confirm('Supprimer les jeûnes sélectionnés ?')"
                        style="display:none;font-size:12px;color:#FCA5A5;padding:6px 14px;border:1px solid rgba(239,68,68,0.3);border-radius:8px;background:rgba(239,68,68,0.1);cursor:pointer;font-weight:600">
                        🗑️ Supprimer la sélection (<span id="selected-count">0</span>)
                    </button>
                </div>

                <div style="overflow-x:auto">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th style="width:40px;padding:10px 12px">
                                    {{-- Tout sélectionner --}}
                                    <input type="checkbox" id="select-all"
                                        style="width:16px;height:16px;cursor:pointer;accent-color:#C9A84C"
                                        title="Tout sélectionner">
                                </th>
                                <th style="padding:12px 16px">Date</th>
                                <th style="padding:12px 16px">Participant</th>
                                <th style="padding:12px 16px">Dirigeant(s)</th>
                                <th style="padding:12px 16px">Type</th>
                                <th style="padding:12px 16px">Temps de prière</th>
                                <th style="padding:12px 16px;text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fasts as $fast)
                                <tr id="row-{{ $fast->id }}">
                                    <td style="padding:10px 12px">
                                        <input type="checkbox" name="ids[]" value="{{ $fast->id }}"
                                            class="fast-checkbox"
                                            style="width:16px;height:16px;cursor:pointer;accent-color:#C9A84C">
                                    </td>
                                    <td class="font-medium">{{ \Carbon\Carbon::parse($fast->fast_date)->format('d/m/Y') }}</td>
                                    <td>{{ $fast->participant?->name ?? '—' }}</td>
                                    <td>
                                        @if($fast->leaders->count())
                                            <div style="display:flex;flex-wrap:wrap;gap:4px">
                                                @foreach($fast->leaders as $leader)
                                                    <span class="badge badge-gold">{{ $leader->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="color:var(--text-muted)">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($fast->fast_type === 'partial')
                                            <span class="badge badge-blue">Partiel</span>
                                        @elseif($fast->fast_type === 'total')
                                            <span class="badge badge-green">Total</span>
                                        @else
                                            <span class="badge">Autre</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($fast->prayer_minutes)
                                            <span style="color:var(--gold)">🙏 {{ $fast->prayer_minutes }} min</span>
                                        @else
                                            <span style="color:var(--text-muted)">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right">
                                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                                            <a href="{{ route('fasts.edit', $fast) }}"
                                                style="font-size:12px;color:var(--blue-bright);text-decoration:none;padding:4px 10px;border:1px solid rgba(79,142,247,0.2);border-radius:6px;background:rgba(79,142,247,0.08)">
                                                Modifier
                                            </a>
                                            <form style="display:inline" method="POST" action="{{ route('fasts.destroy', $fast) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" onclick="return confirm('Supprimer ce jeûne ?')"
                                                    style="font-size:12px;color:#FCA5A5;padding:4px 10px;border:1px solid rgba(239,68,68,0.2);border-radius:6px;background:rgba(239,68,68,0.08);cursor:pointer">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding:4rem;text-align:center">
                                        <div style="font-size:40px;margin-bottom:12px">🕊️</div>
                                        <div style="font-family:'Cinzel',serif;color:var(--gold);font-size:15px;margin-bottom:6px">Aucun jeûne enregistré</div>
                                        <div style="color:var(--text-muted);font-size:13px;margin-bottom:16px">Commence par ajouter ton premier jeûne.</div>
                                        <a href="{{ route('fasts.create') }}" class="app-btn-primary" style="display:inline-flex">+ Ajouter</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <script>
        const checkboxes = document.querySelectorAll('.fast-checkbox');
        const selectAll  = document.getElementById('select-all');
        const bulkBtn    = document.getElementById('bulk-delete-btn');
        const countSpan  = document.getElementById('selected-count');

        function updateBulkBtn() {
            const checked = document.querySelectorAll('.fast-checkbox:checked').length;
            countSpan.textContent = checked;
            bulkBtn.style.display = checked > 0 ? 'block' : 'none';
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkBtn();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', function() {
            selectAll.checked = [...checkboxes].every(c => c.checked);
            updateBulkBtn();
        }));
    </script>
</x-app-layout>