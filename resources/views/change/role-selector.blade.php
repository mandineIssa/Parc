{{-- resources/views/change/role-selector.blade.php --}}
@extends('layouts.change')

@section('title', 'Sélection du rôle')

@section('content')
<div class="role-selector">
    <div class="role-selector-inner">
        <div style="font-size: 40px; margin-bottom: 16px;">🏦</div>
        <div class="role-selector-title">Gestion des Changements</div>
        <div class="role-selector-sub">Change Management · Sélectionnez votre profil</div>
        
        <form method="POST" action="{{ route('change.role.set') }}">
            @csrf
            <div class="role-cards">
                <div class="role-card role-card-N1" onclick="this.querySelector('input').click();">
                    <div class="role-card-icon">📝</div>
                    <div class="role-card-name" style="color: var(--accent);">N+1</div>
                    <div class="role-card-desc">Demandeur<br>Créer et soumettre les formulaires de changement</div>
                    <input type="radio" name="role" value="N1" style="display: none;" onchange="this.form.submit()">
                </div>
                
                <div class="role-card role-card-N2" onclick="this.querySelector('input').click();">
                    <div class="role-card-icon">⚙️</div>
                    <div class="role-card-name" style="color: var(--green);">N+2</div>
                    <div class="role-card-desc">Technicien<br>Compléter, valider ou rejeter les demandes</div>
                    <input type="radio" name="role" value="N2" style="display: none;" onchange="this.form.submit()">
                </div>
                
                <div class="role-card role-card-N3" onclick="this.querySelector('input').click();">
                    <div class="role-card-icon">🔐</div>
                    <div class="role-card-name" style="color: var(--purple);">N+3</div>
                    <div class="role-card-desc">Validateur final<br>Clôturer les tickets approuvés</div>
                    <input type="radio" name="role" value="N3" style="display: none;" onchange="this.form.submit()">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection