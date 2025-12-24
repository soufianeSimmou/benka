@extends('layouts.simple')

@section('title', 'Accueil')

@section('content')
<div class="card">
    <h1>✓ Connexion Réussie !</h1>
    <p>Bienvenue <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</p>
</div>

<div class="card">
    <h2>Menu</h2>
    <p>
        <a href="{{ route('home') }}" class="btn">📋 Accueil</a>
        <a href="{{ route('employees') }}" class="btn">👥 Employés</a>
        <a href="{{ route('history') }}" class="btn">📊 Historique</a>
    </p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger">🚪 Déconnexion</button>
    </form>
</div>

<div class="card">
    <h3>Informations</h3>
    <p>
        <strong>Application :</strong> Présence Chantier<br>
        <strong>Utilisateur :</strong> {{ auth()->user()->email }}<br>
        <strong>Date/Heure :</strong> {{ now()->format('d/m/Y H:i:s') }}
    </p>
</div>
@endsection
