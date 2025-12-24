<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\JobRoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

// ===== PUBLIC ROUTES =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// ===== PROTECTED ROUTES =====
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard route - Main attendance interface
    Route::get('/dashboard', [AttendanceController::class, 'showDashboard'])->name('dashboard');

    // Attendance actions (form-based, not API)
    Route::post('/attendance/load', [AttendanceController::class, 'loadDate'])->name('attendance.load');
    Route::post('/attendance/toggle', [AttendanceController::class, 'toggle'])->name('attendance.toggle');
    Route::post('/attendance/complete', [AttendanceController::class, 'complete'])->name('attendance.complete');
    Route::post('/attendance/navigate', [AttendanceController::class, 'navigate'])->name('attendance.navigate');

    // Job roles management (API routes for AJAX)
    Route::get('/api/job-roles', [JobRoleController::class, 'index'])->name('job-roles.index');
    Route::post('/api/job-roles', [JobRoleController::class, 'store'])->name('job-roles.store');
    Route::put('/api/job-roles/{jobRole}', [JobRoleController::class, 'update'])->name('job-roles.update');
    Route::delete('/api/job-roles/{jobRole}', [JobRoleController::class, 'destroy'])->name('job-roles.destroy');

    // Employee management (API routes for AJAX)
    Route::get('/api/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/api/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/api/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::put('/api/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/api/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/api/employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/employees', function () {
        return view('employees');
    })->name('employees.page');

    Route::get('/history', function () {
        return view('history');
    })->name('history');

    Route::get('/job-roles', function () {
        return view('job-roles');
    })->name('job-roles');

    // Statistics
    Route::get('/statistics', [StatisticsController::class, 'show'])->name('statistics');
    Route::get('/api/statistics/monthly', [StatisticsController::class, 'monthlyStats'])->name('statistics.monthly');
    Route::get('/api/statistics', [StatisticsController::class, 'getStats'])->name('statistics.api');

    // Attendance History API
    Route::get('/api/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/api/attendance/day/{date}', [AttendanceController::class, 'dayDetail'])->name('attendance.day');
    Route::get('/api/attendance/employee-summary', [AttendanceController::class, 'employeeSummary'])->name('attendance.employee-summary');

    // Reopen completed day
    Route::post('/attendance/reopen', [AttendanceController::class, 'reopen'])->name('attendance.reopen');
});
