@extends('layouts.app')

@section('title', 'Ajouter un stagiaire')

@section('content')
    <div class="page-head">
        <div>
            <h1>Ajouter un stagiaire</h1>
            <p class="subtitle">Renseignez les informations nécessaires à l’attestation.</p>
        </div>
    </div>

    <form class="panel panel-body" method="POST" action="{{ route('stagiaires.store') }}">
        @csrf
        @include('stagiaires._form', ['stagiaire' => null, 'submitLabel' => 'Enregistrer'])
    </form>
@endsection
