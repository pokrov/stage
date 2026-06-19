@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="page-head">
        <div>
            <h1>Tableau de bord</h1>
            <p class="subtitle">Gérez les stagiaires et générez leurs attestations.</p>
        </div>
        <a href="{{ route('stagiaires.create') }}" class="btn btn-primary">+ Ajouter un stagiaire</a>
    </div>

    <div class="cards">
        <div class="stat">
            <div class="stat-label">Total des stagiaires</div>
            <div class="stat-value">{{ $total }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Stages en cours</div>
            <div class="stat-value">{{ $enCours }}</div>
        </div>
    </div>

    <section class="panel">
        @if ($stagiaires->isEmpty())
            <div class="empty">
                <h2>Aucun stagiaire enregistré</h2>
                <p>Ajoutez votre premier stagiaire pour commencer.</p>
                <a href="{{ route('stagiaires.create') }}" class="btn btn-primary">Ajouter un stagiaire</a>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Stagiaire</th>
                            <th>Sujet</th>
                            <th>Période</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stagiaires as $stagiaire)
                            <tr
                                class="stagiaire-row"
                                tabindex="0"
                                role="link"
                                aria-label="Voir la fiche de {{ $stagiaire->nom }}"
                                data-href="{{ route('stagiaires.show', $stagiaire) }}"
                            >
                                <td>
                                    <a class="name" href="{{ route('stagiaires.show', $stagiaire) }}">{{ $stagiaire->nom }}</a>
                                    <div class="muted">CIN : {{ $stagiaire->cin }}</div>
                                    <div class="row-hint">Cliquer pour voir la fiche complète</div>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $stagiaire->sujet }}</div>
                                    <div class="muted">
                                        {{ $stagiaire->niveau }}
                                        @if ($stagiaire->filiere) · {{ $stagiaire->filiere }} @endif
                                        · {{ $stagiaire->etablissement }}
                                    </div>
                                </td>
                                <td>
                                    <div class="period">{{ $stagiaire->date_debut->format('d/m/Y') }}</div>
                                    <div class="muted">au {{ $stagiaire->date_fin->format('d/m/Y') }}</div>
                                </td>
                                <td class="actions-cell">
                                    <div class="actions">
                                        <a class="btn btn-success btn-sm" href="{{ route('stagiaires.attestation', $stagiaire) }}">Attestation</a>
                                        <a class="btn btn-light btn-sm" href="{{ route('stagiaires.edit', $stagiaire) }}">Modifier</a>
                                        <form method="POST" action="{{ route('stagiaires.destroy', $stagiaire) }}" onsubmit="return confirm('Supprimer ce stagiaire ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $stagiaires->links() }}</div>
        @endif
    </section>

    <script>
        document.querySelectorAll('.stagiaire-row').forEach((row) => {
            const openDetails = () => window.location.assign(row.dataset.href);

            row.addEventListener('click', (event) => {
                if (event.target.closest('a, button, form, input')) {
                    return;
                }

                openDetails();
            });

            row.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openDetails();
                }
            });
        });
    </script>
@endsection
