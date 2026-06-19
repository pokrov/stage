@extends('layouts.app')

@section('title', 'Modifier le stagiaire')

@section('content')
    <div class="page-head">
        <div>
            <h1>Modifier le stagiaire</h1>
            <p class="subtitle">{{ $stagiaire->nom }}</p>
        </div>
    </div>

    <form class="panel panel-body" method="POST" action="{{ route('stagiaires.update', $stagiaire) }}">
        @csrf
        @method('PUT')
        @include('stagiaires._form', ['submitLabel' => 'Mettre à jour'])
    </form>
@endsection
