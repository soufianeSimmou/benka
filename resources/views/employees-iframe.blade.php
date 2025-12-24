@extends('layouts.iframe')

@section('title', 'Gestion des Employes')
@section('page-name', 'employees')

@php
    // Include the content from employees.blade.php but without the layout
    $content = file_get_contents(resource_path('views/employees.blade.php'));
    // Remove @extends and @section wrappers, keep only content
    $content = preg_replace('/@extends\(.*?\)/', '', $content);
    $content = preg_replace('/@section\(.*?\)/', '', $content);
    $content = preg_replace('/@endsection/', '', $content);
@endphp

@section('content')
{!! $content !!}
@endsection
