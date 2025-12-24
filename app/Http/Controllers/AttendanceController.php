<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\DailyAttendanceStatus;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Show the attendance dashboard
     */
    public function showDashboard(): View
    {
        $date = today()->toDateString();
        return $this->getAttendanceData($date);
    }

    /**
     * Load attendance data for a specific date (POST request from form)
     */
    public function loadDate(Request $request): View
    {
        $date = $request->input('date', today()->toDateString());
        return $this->getAttendanceData($date);
    }

    /**
     * Navigate to previous/next day or selected date (POST request)
     */
    public function navigate(Request $request): View
    {
        // Check if a specific date was selected from the date picker
        if ($request->has('selected_date')) {
            $date = $request->input('selected_date');
            return $this->getAttendanceData($date);
        }

        // Otherwise, handle previous/next navigation
        $currentDate = $request->input('current_date', today()->toDateString());
        $direction = $request->input('direction', 'next');

        $date = Carbon::createFromFormat('Y-m-d', $currentDate);
        if ($direction === 'next') {
            $date->addDay();
        } else {
            $date->subDay();
        }

        return $this->getAttendanceData($date->toDateString());
    }

    /**
     * Toggle attendance status for an employee (POST request)
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');
        $employeeId = $request->input('employee_id');

        $record = AttendanceRecord::firstOrCreate(
            ['employee_id' => $employeeId, 'date' => $date],
            ['status' => 'absent']
        );

        // Toggle status
        $newStatus = $record->status === 'present' ? 'absent' : 'present';

        $updateData = [
            'status' => $newStatus,
            'marked_at' => now(),
        ];

        // Only set marked_by if user is authenticated
        if (auth()->check()) {
            $updateData['marked_by'] = auth()->id();
        }

        $record->update($updateData);

        // Calculate new totals
        $employees = Employee::where('is_active', true)->get();
        $attendance = AttendanceRecord::where('date', $date)->get()->keyBy('employee_id');

        $total = $employees->count();
        $present = $attendance->where('status', 'present')->count();
        $absent = $total - $present;

        return response()->json([
            'success' => true,
            'employee_id' => $employeeId,
            'status' => $newStatus,
            'present' => $present,
            'absent' => $absent,
            'total' => $total,
        ]);
    }

    /**
     * Mark a day as completed (POST request)
     */
    public function complete(Request $request): RedirectResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');

        $data = [
            'is_completed' => true,
            'completed_at' => now(),
        ];

        // Only set completed_by if user is authenticated
        if (auth()->check()) {
            $data['completed_by'] = auth()->id();
        }

        DailyAttendanceStatus::updateOrCreate(
            ['date' => $date],
            $data
        );

        // Redirect to SPA attendance view
        return redirect()->route('spa.view', ['view' => 'attendance'])
            ->with('success', 'Journée marquée comme terminée')
            ->with('date', $date);
    }

    /**
     * Reopen a completed day (POST request)
     */
    public function reopen(Request $request): RedirectResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');

        DailyAttendanceStatus::where('date', $date)->update([
            'is_completed' => false,
            'completed_by' => null,
            'completed_at' => null,
        ]);

        return redirect()->route('spa.view', ['view' => 'attendance'])
            ->with('success', 'Journée rouverte pour modification')
            ->with('date', $date);
    }

    /**
     * Get attendance history for a date range (API)
     */
    public function history(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            return response()->json([]);
        }

        $records = AttendanceRecord::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('date');

        $result = [];
        foreach ($records as $date => $dayRecords) {
            $present = $dayRecords->where('status', 'present')->count();
            $total = $dayRecords->count();
            $result[] = [
                'date' => $date,
                'present' => $present,
                'total' => $total,
            ];
        }

        // Sort by date descending
        usort($result, fn($a, $b) => strcmp($b['date'], $a['date']));

        return response()->json($result);
    }

    /**
     * Get attendance detail for a specific day (API)
     */
    public function dayDetail(string $date): JsonResponse
    {
        $records = AttendanceRecord::where('date', $date)
            ->with('employee')
            ->get();

        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $total = $records->count();

        $employees = $records->map(function ($record) {
            return [
                'id' => $record->employee_id,
                'name' => $record->employee->first_name . ' ' . $record->employee->last_name,
                'status' => $record->status,
            ];
        })->sortBy('name')->values();

        return response()->json([
            'date' => $date,
            'present' => $present,
            'absent' => $absent,
            'total' => $total,
            'employees' => $employees,
        ]);
    }

    /**
     * Get attendance summary by employee (API)
     */
    public function employeeSummary(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            return response()->json([]);
        }

        // Get all active employees with their job roles
        $employees = Employee::where('is_active', true)
            ->with('jobRole')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $result = [];

        foreach ($employees as $employee) {
            // Get all attendance records for this employee in the date range
            $records = AttendanceRecord::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

            // Only include employees that have attendance records in this period
            if ($records->isEmpty()) {
                continue;
            }

            // Get absences with formatted dates
            $absences = $records->where('status', 'absent')->map(function ($record) {
                $date = Carbon::parse($record->date);
                return [
                    'date' => $record->date,
                    'formatted' => $date->locale('fr')->isoFormat('dddd D MMMM'),
                ];
            })->values();

            // Calculate totals
            $totalDays = $records->count();
            $presentDays = $records->where('status', 'present')->count();
            $absentDays = $records->where('status', 'absent')->count();

            $result[] = [
                'id' => $employee->id,
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'job_role' => $employee->jobRole?->name ?? 'Sans métier',
                'absences' => $absences,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
            ];
        }

        return response()->json($result);
    }

    /**
     * Helper method to get and format attendance data for a specific date
     */
    private function getAttendanceData(string $date): View
    {
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
        if ($attendance->isEmpty()) {
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
        $dayStatus = DailyAttendanceStatus::where('date', $date)->first();

        // Group employees by job role
        $employeesByRole = $employees->groupBy(function ($employee) {
            return $employee->jobRole?->name ?? 'Sans métier';
        });

        $totalEmployees = $employees->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $absentCount = $totalEmployees - $presentCount;

        return view('attendance', [
            'date' => $date,
            'employees' => $employees,
            'employeesByRole' => $employeesByRole,
            'attendance' => $attendance,
            'total' => $totalEmployees,
            'present' => $presentCount,
            'absent' => $absentCount,
            'isCompleted' => $dayStatus?->is_completed ?? false,
        ]);
    }
}
