@extends('layouts.app')

@section('title', 'Benka - Gestion de Présence')
@section('page-name', 'spa-dashboard')

@section('content')
<div class="spa-container min-h-screen">
    <!-- Vue Attendance (visible par défaut) -->
    <div id="view-attendance" class="view-container" data-loaded="true">
        <iframe src="/attendance-content" class="w-full h-screen border-0" style="height: calc(100vh - 5rem);"></iframe>
    </div>

    <!-- Vue Employees (cachée) -->
    <div id="view-employees" class="view-container hidden">
        <iframe src="/employees-content" class="w-full h-screen border-0" style="height: calc(100vh - 5rem);"></iframe>
    </div>

    <!-- Vue Job Roles (cachée) -->
    <div id="view-job-roles" class="view-container hidden">
        <iframe src="/job-roles-content" class="w-full h-screen border-0" style="height: calc(100vh - 5rem);"></iframe>
    </div>

    <!-- Vue History (cachée) -->
    <div id="view-history" class="view-container hidden">
        <iframe src="/history-content" class="w-full h-screen border-0" style="height: calc(100vh - 5rem);"></iframe>
    </div>

    <!-- Vue Statistics (cachée) -->
    <div id="view-statistics" class="view-container hidden">
        <iframe src="/statistics-content" class="w-full h-screen border-0" style="height: calc(100vh - 5rem);"></iframe>
    </div>
</div>

<script>
console.log('[SPA Dashboard] All iframes loaded - SPA Manager will handle view switching');
</script>
@endsection
