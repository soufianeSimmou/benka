<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    /**
     * Get all employees
     */
    public function index(): JsonResponse
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

        // Calculate attendance rate from counts
        $employees->each(function ($employee) {
            $totalRecords = $employee->total_present + $employee->total_absent;
            $employee->attendance_rate = $totalRecords > 0
                ? round(($employee->total_present / $totalRecords) * 100)
                : 0;
        });

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
            'job_role_id' => ['required', 'exists:job_roles,id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $employee = Employee::create($validated);
        $employee->load('jobRole');

        return response()->json($employee, 201);
    }

    /**
     * Get a specific employee
     */
    public function show(Employee $employee): JsonResponse
    {
        $employee->load('jobRole', 'attendanceRecords');
        return response()->json($employee);
    }

    /**
     * Update an employee
     */
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'job_role_id' => ['sometimes', 'exists:job_roles,id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $employee->update($validated);
        $employee->load('jobRole');

        return response()->json($employee);
    }

    /**
     * Soft delete an employee
     */
    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();

        return response()->json(['success' => true, 'message' => 'Employee deleted']);
    }

    /**
     * Restore a soft-deleted employee
     */
    public function restore($id): JsonResponse
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->restore();
        $employee->load('jobRole');

        return response()->json(['success' => true, 'employee' => $employee]);
    }
}
