@extends('layouts.app')

@section('title', $stagiaire->nom)

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $stagiaire->nom }}</h1>
            <p class="subtitle">CIN : {{ $stagiaire->cin }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('stagiaires.edit', $stagiaire) }}" class="btn btn-light">Modifier</a>
            <a href="{{ route('stagiaires.attestation', $stagiaire) }}" class="btn btn-success">Générer l’attestation</a>
        </div>
    </div>

    <section class="panel">
        <div class="details">
            <div class="detail">
                <div class="detail-label">Sujet</div>
                <div class="detail-value">{{ $stagiaire->sujet }}</div>
            </div>
            <div class="detail">
                <div class="detail-label">Niveau</div>
                <div class="detail-value">{{ $stagiaire->niveau }}</div>
            </div>
            <div class="detail">
                <div class="detail-label">Filière</div>
                <div class="detail-value">{{ $stagiaire->filiere ?: 'Non renseignée' }}</div>
            </div>
            <div class="detail">
                <div class="detail-label">Établissement</div>
                <div class="detail-value">{{ $stagiaire->etablissement }}</div>
            </div>
            <div class="detail">
                <div class="detail-label">Période du stage</div>
                <div class="detail-value">Du {{ $stagiaire->date_debut->format('d/m/Y') }} au {{ $stagiaire->date_fin->format('d/m/Y') }}</div>
            </div>
        </div>
    </section>
@endsection
