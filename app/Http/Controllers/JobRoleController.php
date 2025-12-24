<?php

namespace App\Http\Controllers;

use App\Models\JobRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobRoleController extends Controller
{
    /**
     * Get all job roles
     */
    public function index(): JsonResponse
    {
        $roles = JobRole::with(['employees' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('last_name')
                    ->orderBy('first_name');
            }])
            ->withCount(['employees' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return response()->json($roles);
    }

    /**
     * Create a new job role
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:job_roles,name'],
            'description' => ['nullable', 'string'],
            'daily_salary' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'display_order' => ['integer'],
        ]);

        $role = JobRole::create($validated);

        return response()->json($role, 201);
    }

    /**
     * Update a job role
     */
    public function update(Request $request, JobRole $jobRole): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'unique:job_roles,name,' . $jobRole->id],
            'description' => ['nullable', 'string'],
            'daily_salary' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'display_order' => ['sometimes', 'integer'],
        ]);

        $jobRole->update($validated);

        return response()->json($jobRole);
    }

    /**
     * Delete a job role
     */
    public function destroy(JobRole $jobRole): JsonResponse
    {
        // Check if job role has employees
        if ($jobRole->employees()->count() > 0) {
            return response()->json(
                ['error' => 'Cannot delete job role with associated employees'],
                422
            );
        }

        $jobRole->delete();

        return response()->json(['success' => true, 'message' => 'Job role deleted']);
    }
}
