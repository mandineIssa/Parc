@extends('layouts.app')

@section('title', 'Liste des postes')
@section('header', 'Liste des postes')

@section('content')
    @include('parametres.partials.referentiel-table', [
        'title' => 'Liste des postes',
        'createUrl' => route('parametres.postes.create'),
        'createLabel' => 'Nouveau poste',
        'items' => $postes,
        'perPage' => $perPage,
        'showRoute' => 'parametres.postes.show',
        'editRoute' => 'parametres.postes.edit',
        'destroyRoute' => 'parametres.postes.destroy',
    ])
@endsection
