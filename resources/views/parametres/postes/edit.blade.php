@extends('layouts.app')
@section('title', 'Modifier le poste')
@section('content')
    @include('parametres.postes.form', ['poste' => $poste])
@endsection
