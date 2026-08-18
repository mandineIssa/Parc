@extends('layouts.app')
@section('title', 'Modifier le département')
@section('content')
    @include('parametres.departements.form', ['departement' => $departement])
@endsection
