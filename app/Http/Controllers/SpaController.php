<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use App\Models\Employee;
use App\Models\JobRole;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class SpaController extends Controller
{
    public function getView(Request $request, string $view): Response
    {
        // Return ONLY view content (no layout) for Alpine.js injection
        switch ($view) {
            case 'attendance':
                return $this->getAttendanceView($request);

            case 'employees':
                return $this->getEmployeesView($request);

            case 'job-roles':
                return $this->getJobRolesView($request);

            case 'history':
                return $this->getHistoryView($request);

            case 'statistics':
                return $this->getStatisticsView($request);

            default:
                abort(404);
        }
    }

    private function getAttendanceView(Request $request): Response
    {
        // Get date from request or use today
        $date = $request->input('date', today()->toDateString());

        // Get all active employees with their job roles
        $employees = Employee::where('is_active', true)
            ->with('jobRole')
            ->orderBy('job_role_id')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Get or create attendance records for this date
        $attendance = AttendanceRecord::where('date', $date)
            ->get()
            ->keyBy('employee_id');

        // If no records exist for this date, create them (all as absent by default)
        if ($attendance->isEmpty() && $employees->isNotEmpty()) {
            // Batch insert all records at once for better performance
            $records = $employees->map(function ($employee) use ($date) {
                return [
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'status' => 'absent',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            AttendanceRecord::insert($records);

            // Reload attendance records
            $attendance = AttendanceRecord::where('date', $date)
                ->get()
                ->keyBy('employee_id');
        }

        // Check if this day is completed
        $dayStatus = \App\Models\DailyAttendanceStatus::where('date', $date)->first();

        // Group employees by job role
        $employeesByRole = $employees->groupBy(function ($employee) {
            return $employee->jobRole?->name ?? 'Sans métier';
        });

        $totalEmployees = $employees->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $absentCount = $totalEmployees - $presentCount;

        // Render ONLY the partial view (no layout)
        $html = view('partials.attendance', [
            'date' => $date,
            'employees' => $employees,
            'employeesByRole' => $employeesByRole,
            'attendance' => $attendance,
            'total' => $totalEmployees,
            'present' => $presentCount,
            'absent' => $absentCount,
            'isCompleted' => $dayStatus?->is_completed ?? false,
        ])->render();

        return response($html);
    }

    private function getEmployeesView(Request $request): Response
    {
        $employees = Employee::with('jobRole')
            ->withCount([
                'attendanceRecords as total_present' => function ($query) {
                    $query->where('status', 'present');
                },
                'attendanceRecords as total_absent' => function ($query) {
                    $query->where('status', 'absent');
                }
            ])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $jobRoles = JobRole::orderBy('name')->get();

        $html = view('partials.employees', compact('employees', 'jobRoles'))->render();

        return response($html);
    }

    private function getJobRolesView(Request $request): Response
    {
        $jobRoles = JobRole::withCount('employees')->orderBy('name')->get();

        $html = view('partials.job-roles', compact('jobRoles'))->render();

        return response($html);
    }

    private function getHistoryView(Request $request): Response
    {
        $records = AttendanceRecord::with(['employee.jobRole'])
            ->whereHas('employee')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->groupBy('date');

        $html = view('partials.history', compact('records'))->render();

        return response($html);
    }

    private function getStatisticsView(Request $request): Response
    {
        $stats = [
            'total_employees' => Employee::count(),
            'total_records' => AttendanceRecord::count(),
            'total_present' => AttendanceRecord::where('status', 'present')->count(),
            'total_absent' => AttendanceRecord::where('status', 'absent')->count(),
        ];

        $recentActivity = AttendanceRecord::with(['employee'])
            ->whereHas('employee')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $html = view('partials.statistics', compact('stats', 'recentActivity'))->render();

        return response($html);
    }
}
