@extends('layouts.app')

@section('title', 'Liste des départements')
@section('header', 'Liste des départements')

@section('content')
    @include('parametres.partials.referentiel-table', [
        'title' => 'Liste des départements',
        'createUrl' => route('parametres.departements.create'),
        'createLabel' => 'Nouveau département',
        'items' => $departements,
        'perPage' => $perPage,
        'showRoute' => 'parametres.departements.show',
        'editRoute' => 'parametres.departements.edit',
        'destroyRoute' => 'parametres.departements.destroy',
    ])
@endsection
