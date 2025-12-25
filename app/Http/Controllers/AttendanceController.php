<?php

namespace App\Http\Controllers;

use App\Services\JsonStorageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private JsonStorageService $jsonStorage;

    public function __construct(JsonStorageService $jsonStorage)
    {
        $this->jsonStorage = $jsonStorage;
    }

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
            'employee_id' => 'required|integer',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');
        $employeeId = $request->input('employee_id');

        // Load attendance data
        $attendanceData = $this->jsonStorage->read('attendance-current.json') ?? ['data' => [], 'version' => 1, 'count' => 0];
        $attendance = &$attendanceData['data'];

        // Load employees data to get employee name
        $employeesData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $employee = null;
        foreach ($employeesData['data'] as $emp) {
            if ($emp['id'] == $employeeId) {
                $employee = $emp;
                break;
            }
        }

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        // Find existing record
        $recordIndex = null;
        $record = null;
        foreach ($attendance as $index => $rec) {
            if ($rec['employee_id'] == $employeeId && substr($rec['date'], 0, 10) === $date) {
                $recordIndex = $index;
                $record = $rec;
                break;
            }
        }

        // If no record exists, create one
        if ($record === null) {
            $maxId = 0;
            foreach ($attendance as $rec) {
                if ($rec['id'] > $maxId) {
                    $maxId = $rec['id'];
                }
            }

            $newStatus = 'present';
            $now = now()->toIso8601String();
            $record = [
                'id' => $maxId + 1,
                'employee_id' => $employeeId,
                'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                'date' => $date . 'T00:00:00.000000Z',
                'status' => $newStatus,
                'marked_by' => auth()->check() ? auth()->id() : null,
                'marked_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $attendance[] = $record;
        } else {
            // Toggle status
            $newStatus = $record['status'] === 'present' ? 'absent' : 'present';
            $now = now()->toIso8601String();

            $attendance[$recordIndex]['status'] = $newStatus;
            $attendance[$recordIndex]['marked_at'] = $now;
            $attendance[$recordIndex]['updated_at'] = $now;
            if (auth()->check()) {
                $attendance[$recordIndex]['marked_by'] = auth()->id();
            }
        }

        // Update count and last_updated
        $attendanceData['count'] = count($attendance);
        $attendanceData['last_updated'] = now()->toIso8601String();

        // Write back to file
        $this->jsonStorage->write('attendance-current.json', $attendanceData);

        // Calculate new totals for active employees
        $activeEmployees = array_filter($employeesData['data'], fn($emp) => $emp['is_active']);
        $total = count($activeEmployees);

        $present = 0;
        foreach ($attendance as $rec) {
            if (substr($rec['date'], 0, 10) === $date && $rec['status'] === 'present') {
                $present++;
            }
        }
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
    public function complete(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');

        // Load daily status data
        $dailyStatusData = $this->jsonStorage->read('daily-status-current.json') ?? ['data' => [], 'version' => 1, 'count' => 0];
        $dailyStatuses = &$dailyStatusData['data'];

        // Find existing status
        $statusIndex = null;
        $status = null;
        foreach ($dailyStatuses as $index => $ds) {
            if (substr($ds['date'], 0, 10) === $date) {
                $statusIndex = $index;
                $status = $ds;
                break;
            }
        }

        $now = now()->toIso8601String();

        if ($status === null) {
            // Create new status
            $maxId = 0;
            foreach ($dailyStatuses as $ds) {
                if ($ds['id'] > $maxId) {
                    $maxId = $ds['id'];
                }
            }

            $status = [
                'id' => $maxId + 1,
                'date' => $date . 'T00:00:00.000000Z',
                'is_completed' => true,
                'completed_by' => auth()->check() ? auth()->id() : null,
                'completed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $dailyStatuses[] = $status;
        } else {
            // Update existing status
            $dailyStatuses[$statusIndex]['is_completed'] = true;
            $dailyStatuses[$statusIndex]['completed_at'] = $now;
            $dailyStatuses[$statusIndex]['updated_at'] = $now;
            if (auth()->check()) {
                $dailyStatuses[$statusIndex]['completed_by'] = auth()->id();
            }
            $status = $dailyStatuses[$statusIndex];
        }

        // Update count and last_updated
        $dailyStatusData['count'] = count($dailyStatuses);
        $dailyStatusData['last_updated'] = now()->toIso8601String();

        // Write back to file
        $this->jsonStorage->write('daily-status-current.json', $dailyStatusData);

        // Return JSON for client-side update
        return response()->json([
            'success' => true,
            'message' => 'Journée marquée comme terminée',
            'date' => $date,
            'status' => [
                'id' => $status['id'],
                'date' => $status['date'],
                'is_completed' => true,
                'completed_by' => $status['completed_by'],
                'completed_at' => $status['completed_at'],
            ]
        ]);
    }

    /**
     * Reopen a completed day (POST request)
     */
    public function reopen(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');

        // Load daily status data
        $dailyStatusData = $this->jsonStorage->read('daily-status-current.json') ?? ['data' => [], 'version' => 1, 'count' => 0];
        $dailyStatuses = &$dailyStatusData['data'];

        // Find existing status
        $statusIndex = null;
        $status = null;
        foreach ($dailyStatuses as $index => $ds) {
            if (substr($ds['date'], 0, 10) === $date) {
                $statusIndex = $index;
                $status = $ds;
                break;
            }
        }

        if ($statusIndex !== null) {
            // Update existing status
            $dailyStatuses[$statusIndex]['is_completed'] = false;
            $dailyStatuses[$statusIndex]['completed_by'] = null;
            $dailyStatuses[$statusIndex]['completed_at'] = null;
            $dailyStatuses[$statusIndex]['updated_at'] = now()->toIso8601String();
            $status = $dailyStatuses[$statusIndex];
        }

        // Update count and last_updated
        $dailyStatusData['count'] = count($dailyStatuses);
        $dailyStatusData['last_updated'] = now()->toIso8601String();

        // Write back to file
        $this->jsonStorage->write('daily-status-current.json', $dailyStatusData);

        // Return JSON for client-side update
        return response()->json([
            'success' => true,
            'message' => 'Journée rouverte pour modification',
            'date' => $date,
            'status' => [
                'id' => $status['id'] ?? null,
                'date' => $status['date'] ?? ($date . 'T00:00:00.000000Z'),
                'is_completed' => false,
                'completed_by' => null,
                'completed_at' => null,
            ]
        ]);
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

        // Load attendance data
        $attendanceData = $this->jsonStorage->read('attendance-current.json') ?? ['data' => []];
        $attendance = $attendanceData['data'];

        // Filter records by date range
        $filtered = array_filter($attendance, function ($rec) use ($startDate, $endDate) {
            $recDate = substr($rec['date'], 0, 10);
            return $recDate >= $startDate && $recDate <= $endDate;
        });

        // Group by date
        $grouped = [];
        foreach ($filtered as $rec) {
            $recDate = substr($rec['date'], 0, 10);
            if (!isset($grouped[$recDate])) {
                $grouped[$recDate] = [];
            }
            $grouped[$recDate][] = $rec;
        }

        $result = [];
        foreach ($grouped as $date => $dayRecords) {
            $present = count(array_filter($dayRecords, fn($r) => $r['status'] === 'present'));
            $total = count($dayRecords);
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
        // Load attendance data
        $attendanceData = $this->jsonStorage->read('attendance-current.json') ?? ['data' => []];
        $attendance = $attendanceData['data'];

        // Filter records for this date
        $records = array_filter($attendance, function ($rec) use ($date) {
            return substr($rec['date'], 0, 10) === $date;
        });

        $present = count(array_filter($records, fn($r) => $r['status'] === 'present'));
        $absent = count(array_filter($records, fn($r) => $r['status'] === 'absent'));
        $total = count($records);

        $employees = [];
        foreach ($records as $record) {
            $employees[] = [
                'id' => $record['employee_id'],
                'name' => $record['employee_name'],
                'status' => $record['status'],
            ];
        }

        // Sort by name
        usort($employees, fn($a, $b) => strcmp($a['name'], $b['name']));

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

        // Load employees data
        $employeesData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $employees = array_filter($employeesData['data'], fn($emp) => $emp['is_active']);

        // Load job roles data
        $jobRolesData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];
        $jobRolesMap = [];
        foreach ($jobRolesData['data'] as $role) {
            $jobRolesMap[$role['id']] = $role['name'];
        }

        // Load attendance data
        $attendanceData = $this->jsonStorage->read('attendance-current.json') ?? ['data' => []];
        $attendance = $attendanceData['data'];

        // Sort employees by last name, first name
        usort($employees, function ($a, $b) {
            $cmp = strcmp($a['last_name'], $b['last_name']);
            if ($cmp === 0) {
                return strcmp($a['first_name'], $b['first_name']);
            }
            return $cmp;
        });

        $result = [];

        foreach ($employees as $employee) {
            // Get all attendance records for this employee in the date range
            $records = array_filter($attendance, function ($rec) use ($employee, $startDate, $endDate) {
                $recDate = substr($rec['date'], 0, 10);
                return $rec['employee_id'] == $employee['id']
                    && $recDate >= $startDate
                    && $recDate <= $endDate;
            });

            // Only include employees that have attendance records in this period
            if (empty($records)) {
                continue;
            }

            // Sort records by date descending
            usort($records, fn($a, $b) => strcmp(substr($b['date'], 0, 10), substr($a['date'], 0, 10)));

            // Get absences with formatted dates
            $absences = [];
            foreach ($records as $record) {
                if ($record['status'] === 'absent') {
                    $recDate = substr($record['date'], 0, 10);
                    $date = Carbon::parse($recDate);
                    $absences[] = [
                        'date' => $recDate,
                        'formatted' => $date->locale('fr')->isoFormat('dddd D MMMM'),
                    ];
                }
            }

            // Calculate totals
            $totalDays = count($records);
            $presentDays = count(array_filter($records, fn($r) => $r['status'] === 'present'));
            $absentDays = count(array_filter($records, fn($r) => $r['status'] === 'absent'));

            $result[] = [
                'id' => $employee['id'],
                'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                'job_role' => $jobRolesMap[$employee['job_role_id']] ?? 'Sans métier',
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
        // Load employees data
        $employeesData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $allEmployees = array_filter($employeesData['data'], fn($emp) => $emp['is_active']);

        // Load job roles data
        $jobRolesData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];
        $jobRolesMap = [];
        foreach ($jobRolesData['data'] as $role) {
            $jobRolesMap[$role['id']] = $role;
        }

        // Enrich employees with job role objects
        $employees = [];
        foreach ($allEmployees as $emp) {
            $empCopy = $emp;
            $empCopy['jobRole'] = $jobRolesMap[$emp['job_role_id']] ?? null;
            $employees[] = $empCopy;
        }

        // Sort employees by job_role_id, last_name, first_name
        usort($employees, function ($a, $b) {
            if ($a['job_role_id'] !== $b['job_role_id']) {
                return $a['job_role_id'] - $b['job_role_id'];
            }
            $cmp = strcmp($a['last_name'], $b['last_name']);
            if ($cmp === 0) {
                return strcmp($a['first_name'], $b['first_name']);
            }
            return $cmp;
        });

        // Load attendance data
        $attendanceData = $this->jsonStorage->read('attendance-current.json') ?? ['data' => [], 'version' => 1, 'count' => 0];
        $attendance = &$attendanceData['data'];

        // Get or create attendance records for this date
        $attendanceByEmployee = [];
        foreach ($attendance as $rec) {
            if (substr($rec['date'], 0, 10) === $date) {
                $attendanceByEmployee[$rec['employee_id']] = $rec;
            }
        }

        // If no records exist for this date, create them (all as absent by default)
        if (empty($attendanceByEmployee)) {
            $maxId = 0;
            foreach ($attendance as $rec) {
                if ($rec['id'] > $maxId) {
                    $maxId = $rec['id'];
                }
            }

            $now = now()->toIso8601String();
            foreach ($employees as $employee) {
                $maxId++;
                $newRecord = [
                    'id' => $maxId,
                    'employee_id' => $employee['id'],
                    'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                    'date' => $date . 'T00:00:00.000000Z',
                    'status' => 'absent',
                    'marked_by' => null,
                    'marked_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $attendance[] = $newRecord;
                $attendanceByEmployee[$employee['id']] = $newRecord;
            }

            // Update count and last_updated
            $attendanceData['count'] = count($attendance);
            $attendanceData['last_updated'] = $now;

            // Write back to file
            $this->jsonStorage->write('attendance-current.json', $attendanceData);
        }

        // Load daily status
        $dailyStatusData = $this->jsonStorage->read('daily-status-current.json') ?? ['data' => []];
        $dayStatus = null;
        foreach ($dailyStatusData['data'] as $ds) {
            if (substr($ds['date'], 0, 10) === $date) {
                $dayStatus = $ds;
                break;
            }
        }

        // Group employees by job role
        $employeesByRole = [];
        foreach ($employees as $employee) {
            $roleName = $employee['jobRole']['name'] ?? 'Sans métier';
            if (!isset($employeesByRole[$roleName])) {
                $employeesByRole[$roleName] = [];
            }
            $employeesByRole[$roleName][] = $employee;
        }

        $totalEmployees = count($employees);
        $presentCount = count(array_filter($attendanceByEmployee, fn($r) => $r['status'] === 'present'));
        $absentCount = $totalEmployees - $presentCount;

        return view('attendance', [
            'date' => $date,
            'employees' => $employees,
            'employeesByRole' => $employeesByRole,
            'attendance' => $attendanceByEmployee,
            'total' => $totalEmployees,
            'present' => $presentCount,
            'absent' => $absentCount,
            'isCompleted' => $dayStatus['is_completed'] ?? false,
        ]);
    }
}
