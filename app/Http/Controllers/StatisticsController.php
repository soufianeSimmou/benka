<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\JobRole;
use App\Models\DailyAttendanceStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Get statistics for a specific month (API endpoint)
     */
    public function getStats(Request $request): JsonResponse
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        // Get all attendance records for this month
        $records = AttendanceRecord::whereBetween('date', [$startDate, $endDate])->get();

        // Calculate working days (days with at least one record)
        $workingDays = $records->pluck('date')->unique()->count();

        // Total present and absent
        $totalPresent = $records->where('status', 'present')->count();
        $totalAbsent = $records->where('status', 'absent')->count();
        $total = $totalPresent + $totalAbsent;
        $averageRate = $total > 0 ? round(($totalPresent / $total) * 100) : 0;

        // Top employees by attendance rate
        $employees = Employee::where('is_active', true)->with('jobRole')->get();
        $topEmployees = $employees->map(function ($employee) use ($records) {
            $empRecords = $records->where('employee_id', $employee->id);
            $present = $empRecords->where('status', 'present')->count();
            $total = $empRecords->count();
            $rate = $total > 0 ? round(($present / $total) * 100) : 0;

            return [
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'rate' => $rate,
                'present' => $present,
                'total' => $total,
            ];
        })->filter(fn($e) => $e['total'] > 0)
          ->sortByDesc('rate')
          ->take(5)
          ->values();

        // Stats by job role
        $jobRoles = JobRole::all();
        $byRole = $jobRoles->map(function ($role) use ($records, $employees) {
            $roleEmployeeIds = $employees->where('job_role_id', $role->id)->pluck('id');
            $roleRecords = $records->whereIn('employee_id', $roleEmployeeIds);
            $present = $roleRecords->where('status', 'present')->count();
            $total = $roleRecords->count();
            $rate = $total > 0 ? round(($present / $total) * 100) : 0;

            return [
                'name' => $role->name,
                'rate' => $rate,
                'present' => $present,
                'total' => $total,
            ];
        })->filter(fn($r) => $r['total'] > 0)->values();

        // Daily stats for calendar
        $dailyStats = [];
        $recordsByDate = $records->groupBy('date');

        // Get all completed days for this month (as string dates)
        $completedDates = DailyAttendanceStatus::whereBetween('date', [$startDate, $endDate])
            ->where('is_completed', true)
            ->get()
            ->map(function ($status) {
                return Carbon::parse($status->date)->toDateString();
            })
            ->toArray();

        // Build daily stats for all days in the month
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateKey = $date->toDateString();
            $dayRecords = $records->where('date', $dateKey);

            $present = $dayRecords->where('status', 'present')->count();
            $total = $dayRecords->count();

            $dailyStats[$dateKey] = [
                'present' => $present,
                'total' => $total,
                'is_completed' => in_array($dateKey, $completedDates),
            ];
        }

        return response()->json([
            'working_days' => $workingDays,
            'average_rate' => $averageRate,
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'top_employees' => $topEmployees,
            'by_role' => $byRole,
            'daily_stats' => $dailyStats,
        ]);
    }

    /**
     * Get statistics for the current month
     */
    public function monthlyStats(): JsonResponse
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        $employees = Employee::where('is_active', true)
            ->with('jobRole')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $stats = $employees->map(function ($employee) use ($year, $month) {
            $startDate = Carbon::createFromDate($year, $month, 1)->toDateString();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

            $records = AttendanceRecord::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $presentDays = $records->where('status', 'present')->count();
            $absentDays = $records->where('status', 'absent')->count();
            $totalDays = $records->count();

            return [
                'id' => $employee->id,
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'job_role' => $employee->jobRole?->name,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'total_days' => $totalDays,
                'attendance_rate' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            ];
        });

        $totalPresent = $stats->sum('present_days');
        $totalAbsent = $stats->sum('absent_days');
        $totalDays = $stats->sum('total_days');
        $overallRate = $totalDays > 0 ? round(($totalPresent / $totalDays) * 100, 2) : 0;

        return response()->json([
            'month' => $now->locale('fr_FR')->monthName,
            'year' => $year,
            'employees' => $stats,
            'summary' => [
                'total_employees' => $employees->count(),
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'total_days' => $totalDays,
                'overall_rate' => $overallRate,
            ]
        ]);
    }

    /**
     * Show statistics page
     */
    public function show(): View
    {
        return view('statistics');
    }
}
