<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
            $user = auth()->user();

            $data = $user->data ?? [
                'employees' => [],
                'jobRoles' => [],
                'attendance' => [],
                'dailyStatus' => [],
            ];

            Log::info('[DATA] Loaded successfully from user.data', [
                'user_id' => $user->id,
                'employees' => count($data['employees'] ?? []),
                'jobRoles' => count($data['jobRoles'] ?? []),
                'attendance' => count($data['attendance'] ?? []),
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

            $user = auth()->user();

            // Save all data to user.data JSON field
            $user->data = $validated;
            $user->save();

            Log::info('[DATA] Saved successfully to user.data', [
                'user_id' => $user->id,
                'employees' => count($validated['employees']),
                'jobRoles' => count($validated['jobRoles']),
                'attendance' => count($validated['attendance']),
            ]);

            return response()->json([
                'success' => true,
                'saved_at' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            Log::error('[DATA] Error saving: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to save data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

}
