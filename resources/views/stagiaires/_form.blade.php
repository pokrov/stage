@if ($errors->any())
    <div class="alert alert-error">Veuillez corriger les champs signalés.</div>
@endif

<div class="form-grid">
    <div class="field">
        <label for="nom">Nom complet</label>
        <input id="nom" name="nom" value="{{ old('nom', $stagiaire?->nom) }}" required autofocus>
        @error('nom') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="cin">CIN</label>
        <input id="cin" name="cin" value="{{ old('cin', $stagiaire?->cin) }}" required>
        @error('cin') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field full">
        <label for="sujet">Sujet du stage</label>
        <input id="sujet" name="sujet" value="{{ old('sujet', $stagiaire?->sujet) }}" required>
        @error('sujet') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="niveau">Niveau</label>
        <input
            type="number"
            id="niveau"
            name="niveau"
            min="1"
            max="5"
            step="1"
            value="{{ old('niveau', $stagiaire ? (int) $stagiaire->niveau : '') }}"
            placeholder="1 à 5"
            required
        >
        <span class="hint">Ex. 1 devient « 1ère année », 4 devient « 4ème année ».</span>
        @error('niveau') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="filiere">Filière</label>
        <input
            id="filiere"
            name="filiere"
            value="{{ old('filiere', $stagiaire?->filiere) }}"
            placeholder="Ex. Génie informatique"
            required
        >
        @error('filiere') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="etablissement">Établissement</label>
        @php
            $etablissementsConnus = collect(config('stagiaires.etablissements'))->flatten();
            $etablissementActuel = old('etablissement', $stagiaire?->etablissement);
            $estAutre = filled($etablissementActuel) && !$etablissementsConnus->contains($etablissementActuel);
            $etablissementSelectionne = old('etablissement') === '__autre__' || $estAutre
                ? '__autre__'
                : $etablissementActuel;
            $autreEtablissement = old(
                'etablissement_autre',
                $estAutre ? $etablissementActuel : ''
            );
        @endphp
        <select id="etablissement" name="etablissement" required>
            <option value="">Sélectionner un établissement</option>
            @foreach (config('stagiaires.etablissements') as $groupe => $etablissements)
                <optgroup label="{{ $groupe }}">
                    @foreach ($etablissements as $etablissement)
                        <option value="{{ $etablissement }}" @selected($etablissementSelectionne === $etablissement)>
                            {{ $etablissement }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
            <option value="__autre__" @selected($etablissementSelectionne === '__autre__')>
                + Autre établissement…
            </option>
        </select>
        @error('etablissement') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field {{ $etablissementSelectionne === '__autre__' ? '' : 'hidden' }}" id="autre-etablissement-field">
        <label for="etablissement_autre">Nom du nouvel établissement</label>
        <input
            id="etablissement_autre"
            name="etablissement_autre"
            value="{{ $autreEtablissement }}"
            placeholder="Saisir le nom ou l’acronyme"
        >
        <span class="hint">Ce nom sera enregistré sur la fiche et dans l’attestation.</span>
        @error('etablissement_autre') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="date_debut">Date de début</label>
        <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', $stagiaire?->date_debut?->format('Y-m-d')) }}" required>
        @error('date_debut') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="date_fin">Date de fin</label>
        <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin', $stagiaire?->date_fin?->format('Y-m-d')) }}" required>
        @error('date_fin') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<script>
    (() => {
        const select = document.getElementById('etablissement');
        const field = document.getElementById('autre-etablissement-field');
        const input = document.getElementById('etablissement_autre');

        const updateOtherField = () => {
            const visible = select.value === '__autre__';
            field.classList.toggle('hidden', !visible);
            input.required = visible;

            if (!visible) {
                input.value = '';
            }
        };

        select.addEventListener('change', updateOtherField);
        updateOtherField();
    })();
</script>

<div class="form-actions">
    <a href="{{ route('stagiaires.index') }}" class="btn btn-light">Annuler</a>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
