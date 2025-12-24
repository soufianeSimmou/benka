@extends('layouts.app')

@section('title', 'Benka - Gestion de Présence')
@section('page-name', 'spa-dashboard')

@section('content')
<!-- Container SPA avec toutes les vues -->
<div class="spa-container">
    <!-- Vue Attendance (visible par défaut) -->
    <div id="view-attendance" class="view-container">
        <!-- Le contenu d'attendance sera ici -->
        {!! $attendanceHTML !!}
    </div>

    <!-- Vue Employees (cachée, chargement lazy) -->
    <div id="view-employees" class="view-container hidden"></div>

    <!-- Vue Job Roles (cachée, chargement lazy) -->
    <div id="view-job-roles" class="view-container hidden"></div>

    <!-- Vue History (cachée, chargement lazy) -->
    <div id="view-history" class="view-container hidden"></div>

    <!-- Vue Statistics (cachée, chargement lazy) -->
    <div id="view-statistics" class="view-container hidden"></div>
</div>
@endsection
