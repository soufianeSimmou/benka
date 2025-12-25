<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\JobRole;
use App\Models\AttendanceRecord;
use App\Models\DailyAttendanceStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    /**
     * Load ALL data on app start
     * GET /api/data/load
     */
    public function load(): JsonResponse
    {
        try {
            $data = [
                'employees' => Employee::withTrashed()
                    ->with('jobRole')
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->get(),

                'jobRoles' => JobRole::orderBy('display_order')->get(),

                'attendance' => AttendanceRecord::with('employee')
                    ->orderBy('date', 'desc')
                    ->get(),

                'dailyStatus' => DailyAttendanceStatus::orderBy('date', 'desc')->get(),
            ];

            Log::info('[DATA] Loaded successfully', [
                'employees' => $data['employees']->count(),
                'jobRoles' => $data['jobRoles']->count(),
                'attendance' => $data['attendance']->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'loaded_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('[DATA] Error loading: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to load data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

    /**
     * Save ALL data on app close
     * POST /api/data/save
     */
    public function save(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'employees' => 'required|array',
                'jobRoles' => 'required|array',
                'attendance' => 'required|array',
                'dailyStatus' => 'required|array',
            ]);

            DB::beginTransaction();

            // Sync Job Roles
            $this->syncJobRoles($validated['jobRoles']);

            // Sync Employees
            $this->syncEmployees($validated['employees']);

            // Sync Attendance
            $this->syncAttendance($validated['attendance']);

            // Sync Daily Status
            $this->syncDailyStatus($validated['dailyStatus']);

            DB::commit();

            Log::info('[DATA] Saved successfully', [
                'employees' => count($validated['employees']),
                'jobRoles' => count($validated['jobRoles']),
                'attendance' => count($validated['attendance']),
            ]);

            return response()->json([
                'success' => true,
                'saved_at' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[DATA] Error saving: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to save data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

    private function syncJobRoles(array $roles): void
    {
        $existingIds = JobRole::pluck('id')->toArray();
        $incomingIds = array_column($roles, 'id');

        // Delete removed roles
        $toDelete = array_diff($existingIds, $incomingIds);
        if (!empty($toDelete)) {
            JobRole::whereIn('id', $toDelete)->delete();
        }

        // Upsert all roles
        foreach ($roles as $role) {
            JobRole::updateOrCreate(
                ['id' => $role['id']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'] ?? null,
                    'daily_salary' => $role['daily_salary'] ?? 0,
                    'hourly_rate' => $role['hourly_rate'] ?? 0,
                    'display_order' => $role['display_order'] ?? 0,
                ]
            );
        }
    }

    private function syncEmployees(array $employees): void
    {
        foreach ($employees as $emp) {
            // Skip if no ID (shouldn't happen but be safe)
            if (!isset($emp['id'])) continue;

            $data = [
                'first_name' => $emp['first_name'] ?? '',
                'last_name' => $emp['last_name'] ?? '',
                'job_role_id' => $emp['job_role_id'] ?? null,
                'phone' => $emp['phone'] ?? null,
                'is_active' => isset($emp['is_active']) ? (bool)$emp['is_active'] : true,
            ];

            // Handle soft delete
            if (!empty($emp['deleted_at'])) {
                $data['deleted_at'] = $emp['deleted_at'];
            } else {
                $data['deleted_at'] = null;
            }

            Employee::withTrashed()->updateOrCreate(
                ['id' => $emp['id']],
                $data
            );
        }
    }

    private function syncAttendance(array $records): void
    {
        $existingIds = AttendanceRecord::pluck('id')->toArray();
        $incomingIds = array_column($records, 'id');

        // Delete removed records
        $toDelete = array_diff($existingIds, $incomingIds);
        if (!empty($toDelete)) {
            AttendanceRecord::whereIn('id', $toDelete)->delete();
        }

        // Upsert all records
        foreach ($records as $record) {
            AttendanceRecord::updateOrCreate(
                ['id' => $record['id']],
                [
                    'employee_id' => $record['employee_id'],
                    'date' => $record['date'],
                    'status' => $record['status'],
                    'marked_by' => $record['marked_by'] ?? null,
                    'marked_at' => $record['marked_at'] ?? null,
                ]
            );
        }
    }

    private function syncDailyStatus(array $statuses): void
    {
        foreach ($statuses as $status) {
            DailyAttendanceStatus::updateOrCreate(
                ['date' => $status['date']],
                [
                    'is_completed' => $status['is_completed'] ?? false,
                    'completed_by' => $status['completed_by'] ?? null,
                    'completed_at' => $status['completed_at'] ?? null,
                ]
            );
        }
    }
}
