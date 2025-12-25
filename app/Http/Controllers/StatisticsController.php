<?php

namespace App\Http\Controllers;

use App\Services\JsonStorageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    private JsonStorageService $jsonStorage;

    public function __construct(JsonStorageService $jsonStorage)
    {
        $this->jsonStorage = $jsonStorage;
    }

    /**
     * Get statistics for a specific month (API endpoint)
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

            // Load data from JSON files
            $attendanceData = $this->jsonStorage->read('attendance-current.json');
            $employeesData = $this->jsonStorage->read('employees.json');
            $jobRolesData = $this->jsonStorage->read('job-roles.json');
            $dailyStatusData = $this->jsonStorage->read('daily-status-current.json');

            $records = $attendanceData['data'] ?? [];
            $employees = $employeesData['data'] ?? [];
            $jobRoles = $jobRolesData['data'] ?? [];
            $dailyStatuses = $dailyStatusData['data'] ?? [];

            // Filter records for this month
            $monthRecords = array_filter($records, function($record) use ($startDate, $endDate) {
                $recordDate = Carbon::parse($record['date'])->toDateString();
                return $recordDate >= $startDate && $recordDate <= $endDate;
            });

            // Calculate working days (days with at least one record)
            $uniqueDates = array_unique(array_map(function($record) {
                return Carbon::parse($record['date'])->toDateString();
            }, $monthRecords));
            $workingDays = count($uniqueDates);

            // Total present and absent
            $totalPresent = count(array_filter($monthRecords, fn($r) => $r['status'] === 'present'));
            $totalAbsent = count(array_filter($monthRecords, fn($r) => $r['status'] === 'absent'));
            $total = $totalPresent + $totalAbsent;
            $averageRate = $total > 0 ? round(($totalPresent / $total) * 100) : 0;

            // Top employees by attendance rate
            $topEmployees = $this->calculateTopEmployees($monthRecords, $employees);

            // Stats by job role
            $byRole = $this->calculateStatsByRole($monthRecords, $employees, $jobRoles);

            // Daily stats for calendar
            $dailyStats = $this->calculateDailyStats($monthRecords, $dailyStatuses, $startDate, $endDate);

            return response()->json([
                'working_days' => $workingDays,
                'average_rate' => $averageRate,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'top_employees' => $topEmployees,
                'by_role' => $byRole,
                'daily_stats' => $dailyStats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Statistics API error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'year' => $year ?? null,
                'month' => $month ?? null,
            ]);

            return response()->json([
                'error' => 'Unable to fetch statistics',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'working_days' => 0,
                'average_rate' => 0,
                'total_present' => 0,
                'total_absent' => 0,
                'top_employees' => [],
                'by_role' => [],
                'daily_stats' => [],
            ], 500);
        }
    }

    /**
     * Get statistics for the current month
     */
    public function monthlyStats(): JsonResponse
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        $startDate = Carbon::createFromDate($year, $month, 1)->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        // Load data from JSON files
        $attendanceData = $this->jsonStorage->read('attendance-current.json');
        $employeesData = $this->jsonStorage->read('employees.json');
        $jobRolesData = $this->jsonStorage->read('job-roles.json');

        $records = $attendanceData['data'] ?? [];
        $employees = $employeesData['data'] ?? [];
        $jobRoles = $jobRolesData['data'] ?? [];

        // Create job role lookup map
        $jobRoleMap = [];
        foreach ($jobRoles as $role) {
            $jobRoleMap[$role['id']] = $role['name'];
        }

        // Filter records for this month
        $monthRecords = array_filter($records, function($record) use ($startDate, $endDate) {
            $recordDate = Carbon::parse($record['date'])->toDateString();
            return $recordDate >= $startDate && $recordDate <= $endDate;
        });

        // Group records by employee_id
        $recordsByEmployee = [];
        foreach ($monthRecords as $record) {
            $empId = $record['employee_id'];
            if (!isset($recordsByEmployee[$empId])) {
                $recordsByEmployee[$empId] = [];
            }
            $recordsByEmployee[$empId][] = $record;
        }

        // Calculate stats for each active employee
        $stats = [];
        foreach ($employees as $employee) {
            // Skip inactive or deleted employees
            if (!$employee['is_active'] || $employee['deleted_at'] !== null) {
                continue;
            }

            $empId = $employee['id'];
            $empRecords = $recordsByEmployee[$empId] ?? [];

            $presentDays = count(array_filter($empRecords, fn($r) => $r['status'] === 'present'));
            $absentDays = count(array_filter($empRecords, fn($r) => $r['status'] === 'absent'));
            $totalDays = count($empRecords);

            $stats[] = [
                'id' => $empId,
                'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                'job_role' => $jobRoleMap[$employee['job_role_id']] ?? 'Unknown',
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'total_days' => $totalDays,
                'attendance_rate' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
            ];
        }

        // Sort by last name, then first name
        usort($stats, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $totalPresent = array_sum(array_column($stats, 'present_days'));
        $totalAbsent = array_sum(array_column($stats, 'absent_days'));
        $totalDays = array_sum(array_column($stats, 'total_days'));
        $overallRate = $totalDays > 0 ? round(($totalPresent / $totalDays) * 100, 2) : 0;

        return response()->json([
            'month' => $now->locale('fr_FR')->monthName,
            'year' => $year,
            'employees' => array_values($stats),
            'summary' => [
                'total_employees' => count($stats),
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

    /**
     * Calculate top employees by attendance rate
     */
    private function calculateTopEmployees(array $records, array $employees): array
    {
        // Create employee lookup map
        $employeeMap = [];
        foreach ($employees as $emp) {
            if ($emp['is_active'] && $emp['deleted_at'] === null) {
                $employeeMap[$emp['id']] = $emp;
            }
        }

        // Group records by employee_id
        $recordsByEmployee = [];
        foreach ($records as $record) {
            $empId = $record['employee_id'];
            if (!isset($employeeMap[$empId])) {
                continue; // Skip inactive/deleted employees
            }
            if (!isset($recordsByEmployee[$empId])) {
                $recordsByEmployee[$empId] = [];
            }
            $recordsByEmployee[$empId][] = $record;
        }

        // Calculate rate for each employee
        $employeeStats = [];
        foreach ($recordsByEmployee as $empId => $empRecords) {
            $present = count(array_filter($empRecords, fn($r) => $r['status'] === 'present'));
            $total = count($empRecords);

            if ($total > 0) {
                $employee = $employeeMap[$empId];
                $employeeStats[] = [
                    'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                    'rate' => (int)round(($present / $total) * 100),
                    'present' => $present,
                    'total' => $total,
                ];
            }
        }

        // Sort by rate descending and limit to top 5
        usort($employeeStats, fn($a, $b) => $b['rate'] - $a['rate']);
        return array_slice($employeeStats, 0, 5);
    }

    /**
     * Calculate statistics by job role
     */
    private function calculateStatsByRole(array $records, array $employees, array $jobRoles): array
    {
        // Create employee and job role lookup maps
        $employeeMap = [];
        foreach ($employees as $emp) {
            if ($emp['is_active'] && $emp['deleted_at'] === null) {
                $employeeMap[$emp['id']] = $emp;
            }
        }

        $jobRoleMap = [];
        foreach ($jobRoles as $role) {
            $jobRoleMap[$role['id']] = $role['name'];
        }

        // Group records by job role
        $recordsByRole = [];
        foreach ($records as $record) {
            $empId = $record['employee_id'];
            if (!isset($employeeMap[$empId])) {
                continue; // Skip inactive/deleted employees
            }

            $employee = $employeeMap[$empId];
            $roleId = $employee['job_role_id'];
            $roleName = $jobRoleMap[$roleId] ?? 'Unknown';

            if (!isset($recordsByRole[$roleName])) {
                $recordsByRole[$roleName] = [];
            }
            $recordsByRole[$roleName][] = $record;
        }

        // Calculate stats for each role
        $roleStats = [];
        foreach ($recordsByRole as $roleName => $roleRecords) {
            $present = count(array_filter($roleRecords, fn($r) => $r['status'] === 'present'));
            $total = count($roleRecords);

            if ($total > 0) {
                $roleStats[] = [
                    'name' => $roleName,
                    'rate' => (int)round(($present / $total) * 100),
                    'present' => $present,
                    'total' => $total,
                ];
            }
        }

        return $roleStats;
    }

    /**
     * Calculate daily statistics for calendar
     */
    private function calculateDailyStats(array $records, array $dailyStatuses, string $startDate, string $endDate): array
    {
        // Group records by date
        $recordsByDate = [];
        foreach ($records as $record) {
            $dateKey = Carbon::parse($record['date'])->toDateString();
            if (!isset($recordsByDate[$dateKey])) {
                $recordsByDate[$dateKey] = [];
            }
            $recordsByDate[$dateKey][] = $record;
        }

        // Get completed dates
        $completedDates = [];
        foreach ($dailyStatuses as $status) {
            if ($status['is_completed']) {
                $dateKey = Carbon::parse($status['date'])->toDateString();
                $completedDates[$dateKey] = true;
            }
        }

        // Build daily stats for all days in the month
        $dailyStats = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateKey = $date->toDateString();
            $dayRecords = $recordsByDate[$dateKey] ?? [];

            $present = count(array_filter($dayRecords, fn($r) => $r['status'] === 'present'));
            $total = count($dayRecords);

            $dailyStats[$dateKey] = [
                'present' => $present,
                'total' => $total,
                'is_completed' => isset($completedDates[$dateKey]),
            ];
        }

        return $dailyStats;
    }
}
