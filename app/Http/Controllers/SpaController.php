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
        $controller = app(AttendanceController::class);

        // Get the view content from the controller
        $viewData = $controller->showDashboard();

        // Extract just the @section('content') part
        $html = view('partials.attendance', $viewData->getData())->render();

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
