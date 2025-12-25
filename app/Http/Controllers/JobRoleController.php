<?php

namespace App\Http\Controllers;

use App\Services\JsonStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobRoleController extends Controller
{
    private JsonStorageService $jsonStorage;

    public function __construct(JsonStorageService $jsonStorage)
    {
        $this->jsonStorage = $jsonStorage;
    }

    /**
     * Get all job roles
     */
    public function index(): JsonResponse
    {
        $data = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];

        return response()->json($data['data']);
    }

    /**
     * Create a new job role
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'daily_salary' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'display_order' => ['integer'],
        ]);

        // Read current data
        $fileData = $this->jsonStorage->read('job-roles.json') ?? [
            'version' => 1,
            'last_updated' => now()->toIso8601String(),
            'data' => []
        ];

        $roles = $fileData['data'];

        // Check if name already exists
        foreach ($roles as $role) {
            if (strtolower($role['name']) === strtolower($validated['name'])) {
                return response()->json([
                    'error' => 'The name has already been taken.',
                    'errors' => ['name' => ['The name has already been taken.']]
                ], 422);
            }
        }

        // Generate new ID
        $maxId = 0;
        foreach ($roles as $role) {
            if (isset($role['id']) && $role['id'] > $maxId) {
                $maxId = $role['id'];
            }
        }

        $newRole = [
            'id' => $maxId + 1,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'daily_salary' => isset($validated['daily_salary']) ? (float)$validated['daily_salary'] : 0.0,
            'hourly_rate' => isset($validated['hourly_rate']) ? (float)$validated['hourly_rate'] : 0.0,
            'display_order' => $validated['display_order'] ?? count($roles),
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        $roles[] = $newRole;

        // Update file
        $fileData['data'] = $roles;
        $fileData['last_updated'] = now()->toIso8601String();
        $fileData['count'] = count($roles);

        $this->jsonStorage->write('job-roles.json', $fileData);

        return response()->json($newRole, 201);
    }

    /**
     * Update a job role
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'daily_salary' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'display_order' => ['sometimes', 'integer'],
        ]);

        // Read current data
        $fileData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];
        $roles = $fileData['data'];

        // Find the role to update
        $roleIndex = -1;
        foreach ($roles as $index => $role) {
            if ($role['id'] == $id) {
                $roleIndex = $index;
                break;
            }
        }

        if ($roleIndex === -1) {
            return response()->json(['error' => 'Job role not found'], 404);
        }

        // Check if name already exists (excluding current role)
        if (isset($validated['name'])) {
            foreach ($roles as $index => $role) {
                if ($index !== $roleIndex && strtolower($role['name']) === strtolower($validated['name'])) {
                    return response()->json([
                        'error' => 'The name has already been taken.',
                        'errors' => ['name' => ['The name has already been taken.']]
                    ], 422);
                }
            }
        }

        // Update role
        $roles[$roleIndex] = array_merge($roles[$roleIndex], $validated);
        $roles[$roleIndex]['updated_at'] = now()->toIso8601String();

        // Update file
        $fileData['data'] = $roles;
        $fileData['last_updated'] = now()->toIso8601String();

        $this->jsonStorage->write('job-roles.json', $fileData);

        return response()->json($roles[$roleIndex]);
    }

    /**
     * Delete a job role
     */
    public function destroy(int $id): JsonResponse
    {
        // Read current data
        $fileData = $this->jsonStorage->read('job-roles.json') ?? ['data' => []];
        $roles = $fileData['data'];

        // Check if role has employees
        $employeesData = $this->jsonStorage->read('employees.json') ?? ['data' => []];
        $hasEmployees = false;
        foreach ($employeesData['data'] as $employee) {
            if ($employee['job_role_id'] == $id && !isset($employee['deleted_at'])) {
                $hasEmployees = true;
                break;
            }
        }

        if ($hasEmployees) {
            return response()->json(
                ['error' => 'Cannot delete job role with associated employees'],
                422
            );
        }

        // Remove role
        $newRoles = [];
        $found = false;
        foreach ($roles as $role) {
            if ($role['id'] != $id) {
                $newRoles[] = $role;
            } else {
                $found = true;
            }
        }

        if (!$found) {
            return response()->json(['error' => 'Job role not found'], 404);
        }

        // Update file
        $fileData['data'] = $newRoles;
        $fileData['last_updated'] = now()->toIso8601String();
        $fileData['count'] = count($newRoles);

        $this->jsonStorage->write('job-roles.json', $fileData);

        return response()->json(['success' => true, 'message' => 'Job role deleted']);
    }
}
