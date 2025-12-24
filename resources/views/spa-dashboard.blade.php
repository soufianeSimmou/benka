@extends('layouts.app')

@section('title', 'Benka - Gestion de Présence')
@section('page-name', 'spa-dashboard')

@section('content')
<div class="spa-container min-h-screen">
    <!-- Vue Attendance (visible par défaut) - Contenu chargé au démarrage -->
    <div id="view-attendance" class="view-container">
        @include('attendance-content')
    </div>

    <!-- Vue Employees (cachée) - Contenu chargé dynamiquement -->
    <div id="view-employees" class="view-container hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="loading loading-spinner loading-lg"></div>
        </div>
    </div>

    <!-- Vue Job Roles (cachée) - Contenu chargé dynamiquement -->
    <div id="view-job-roles" class="view-container hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="loading loading-spinner loading-lg"></div>
        </div>
    </div>

    <!-- Vue History (cachée) - Contenu chargé dynamiquement -->
    <div id="view-history" class="view-container hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="loading loading-spinner loading-lg"></div>
        </div>
    </div>

    <!-- Vue Statistics (cachée) - Contenu chargé dynamiquement -->
    <div id="view-statistics" class="view-container hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="loading loading-spinner loading-lg"></div>
        </div>
    </div>
</div>
@endsection
