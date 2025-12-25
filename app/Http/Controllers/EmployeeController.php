<?php

namespace App\Http\Controllers;

use App\Services\JsonStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    private JsonStorageService $jsonStorage;

    public function __construct(JsonStorageService $jsonStorage)
    {
        $this->jsonStorage = $jsonStorage;
    }

    /**
     * Get all employees
     */
    public function index(): JsonResponse
    {
        $employeesData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $attendanceData = $this->jsonStorage->read('attendance-current.json') ?? ['data' => []];
        $jobRolesData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];

        $employees = $employeesData['data'];
        $attendance = $attendanceData['data'];

        // Add job role info and calculate attendance stats
        foreach ($employees as &$employee) {
            // Add job role name
            foreach ($jobRolesData['data'] as $role) {
                if ($role['id'] == $employee['job_role_id']) {
                    $employee['job_role'] = $role;
                    break;
                }
            }

            // Calculate attendance stats
            $totalPresent = 0;
            $totalAbsent = 0;
            foreach ($attendance as $record) {
                if ($record['employee_id'] == $employee['id']) {
                    if ($record['status'] === 'present') {
                        $totalPresent++;
                    } else {
                        $totalAbsent++;
                    }
                }
            }

            $totalRecords = $totalPresent + $totalAbsent;
            $employee['total_present'] = $totalPresent;
            $employee['total_absent'] = $totalAbsent;
            $employee['attendance_rate'] = $totalRecords > 0
                ? round(($totalPresent / $totalRecords) * 100)
                : 0;
        }

        return response()->json($employees);
    }

    /**
     * Create a new employee
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'job_role_id' => ['required', 'integer'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Verify job role exists
        $jobRolesData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];
        $roleExists = false;
        foreach ($jobRolesData['data'] as $role) {
            if ($role['id'] == $validated['job_role_id']) {
                $roleExists = true;
                $validated['job_role_name'] = $role['name'];
                break;
            }
        }

        if (!$roleExists) {
            return response()->json([
                'error' => 'Job role not found',
                'errors' => ['job_role_id' => ['The selected job role is invalid.']]
            ], 422);
        }

        // Read current employees
        $fileData = $this->jsonStorage->read('employees.json') ?? [
            'version' => 1,
            'last_updated' => now()->toIso8601String(),
            'data' => []
        ];

        $employees = $fileData['data'];

        // Generate new ID
        $maxId = 0;
        foreach ($employees as $emp) {
            if (isset($emp['id']) && $emp['id'] > $maxId) {
                $maxId = $emp['id'];
            }
        }

        $newEmployee = [
            'id' => $maxId + 1,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'job_role_id' => $validated['job_role_id'],
            'job_role_name' => $validated['job_role_name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
            'deleted_at' => null,
        ];

        $employees[] = $newEmployee;

        // Update file
        $fileData['data'] = $employees;
        $fileData['last_updated'] = now()->toIso8601String();
        $fileData['count'] = count($employees);

        $this->jsonStorage->write('employees.json', $fileData);

        return response()->json($newEmployee, 201);
    }

    /**
     * Get a specific employee
     */
    public function show(int $id): JsonResponse
    {
        $employeesData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $attendanceData = $this->jsonStorage->read('attendance-current.json') ?? ['data' => []];
        $jobRolesData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];

        // Find employee
        $employee = null;
        foreach ($employeesData['data'] as $emp) {
            if ($emp['id'] == $id) {
                $employee = $emp;
                break;
            }
        }

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Add job role
        foreach ($jobRolesData['data'] as $role) {
            if ($role['id'] == $employee['job_role_id']) {
                $employee['job_role'] = $role;
                break;
            }
        }

        // Add attendance records
        $employee['attendance_records'] = [];
        foreach ($attendanceData['data'] as $record) {
            if ($record['employee_id'] == $id) {
                $employee['attendance_records'][] = $record;
            }
        }

        return response()->json($employee);
    }

    /**
     * Update an employee
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'job_role_id' => ['sometimes', 'integer'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Verify job role exists if provided
        if (isset($validated['job_role_id'])) {
            $jobRolesData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];
            $roleExists = false;
            foreach ($jobRolesData['data'] as $role) {
                if ($role['id'] == $validated['job_role_id']) {
                    $roleExists = true;
                    $validated['job_role_name'] = $role['name'];
                    break;
                }
            }

            if (!$roleExists) {
                return response()->json([
                    'error' => 'Job role not found',
                    'errors' => ['job_role_id' => ['The selected job role is invalid.']]
                ], 422);
            }
        }

        // Read current data
        $fileData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $employees = $fileData['data'];

        // Find employee
        $employeeIndex = -1;
        foreach ($employees as $index => $emp) {
            if ($emp['id'] == $id) {
                $employeeIndex = $index;
                break;
            }
        }

        if ($employeeIndex === -1) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Update employee
        $employees[$employeeIndex] = array_merge($employees[$employeeIndex], $validated);
        $employees[$employeeIndex]['updated_at'] = now()->toIso8601String();

        // Update file
        $fileData['data'] = $employees;
        $fileData['last_updated'] = now()->toIso8601String();

        $this->jsonStorage->write('employees.json', $fileData);

        return response()->json($employees[$employeeIndex]);
    }

    /**
     * Soft delete an employee
     */
    public function destroy(int $id): JsonResponse
    {
        // Read current data
        $fileData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $employees = $fileData['data'];

        // Find employee
        $employeeIndex = -1;
        foreach ($employees as $index => $emp) {
            if ($emp['id'] == $id) {
                $employeeIndex = $index;
                break;
            }
        }

        if ($employeeIndex === -1) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Soft delete
        $employees[$employeeIndex]['deleted_at'] = now()->toIso8601String();
        $employees[$employeeIndex]['is_active'] = false;

        // Update file
        $fileData['data'] = $employees;
        $fileData['last_updated'] = now()->toIso8601String();

        $this->jsonStorage->write('employees.json', $fileData);

        return response()->json(['success' => true, 'message' => 'Employee deleted']);
    }

    /**
     * Restore a soft-deleted employee
     */
    public function restore(int $id): JsonResponse
    {
        // Read current data
        $fileData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $employees = $fileData['data'];

        // Find employee
        $employeeIndex = -1;
        foreach ($employees as $index => $emp) {
            if ($emp['id'] == $id && isset($emp['deleted_at']) && $emp['deleted_at'] !== null) {
                $employeeIndex = $index;
                break;
            }
        }

        if ($employeeIndex === -1) {
            return response()->json(['error' => 'Employee not found or not deleted'], 404);
        }

        // Restore
        $employees[$employeeIndex]['deleted_at'] = null;
        $employees[$employeeIndex]['is_active'] = true;

        // Update file
        $fileData['data'] = $employees;
        $fileData['last_updated'] = now()->toIso8601String();

        $this->jsonStorage->write('employees.json', $fileData);

        return response()->json(['success' => true, 'employee' => $employees[$employeeIndex]]);
    }
}
