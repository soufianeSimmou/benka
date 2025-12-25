<?php

namespace App\Http\Controllers;

use App\Services\JsonStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JsonDataController extends Controller
{
    private JsonStorageService $jsonStorage;

    public function __construct(JsonStorageService $jsonStorage)
    {
        $this->jsonStorage = $jsonStorage;
    }

    /**
     * Load all JSON data (called on app start)
     * GET /api/json/load
     */
    public function load(): JsonResponse
    {
        try {
            $data = $this->jsonStorage->loadAll();

            Log::info('[JSON] Data loaded successfully', [
                'employees_count' => count($data['employees']['data'] ?? []),
                'jobRoles_count' => count($data['jobRoles']['data'] ?? []),
                'attendance_count' => count($data['attendance']['data'] ?? []),
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'loaded_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('[JSON] Error loading data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to load data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

    /**
     * Save all JSON data (called on app close/beforeunload)
     * POST /api/json/save
     */
    public function save(Request $request): JsonResponse
    {
        try {
            $allData = $request->validate([
                'employees' => 'required|array',
                'employees.data' => 'array',
                'employees.version' => 'integer',
                'employees.last_updated' => 'string',

                'jobRoles' => 'required|array',
                'jobRoles.data' => 'array',

                'attendance' => 'required|array',
                'attendance.data' => 'array',

                'dailyStatus' => 'required|array',
                'dailyStatus.data' => 'array',

                'metadata' => 'array',
                'trigger' => 'string' // 'beforeunload', 'manual', 'periodic'
            ]);

            $success = $this->jsonStorage->saveAll($allData);

            if ($success) {
                Log::info('[JSON] Data saved successfully', [
                    'trigger' => $allData['trigger'] ?? 'unknown',
                    'employees_count' => count($allData['employees']['data'] ?? []),
                    'attendance_count' => count($allData['attendance']['data'] ?? []),
                ]);

                return response()->json([
                    'success' => true,
                    'saved_at' => now()->toIso8601String()
                ]);
            } else {
                throw new \Exception('Failed to save one or more files');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('[JSON] Validation error during save', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid data format',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('[JSON] Error saving data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to save data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

    /**
     * Manual save trigger (for testing or explicit saves)
     * POST /api/json/save-now
     */
    public function saveNow(Request $request): JsonResponse
    {
        $request->merge(['trigger' => 'manual']);
        return $this->save($request);
    }
}
