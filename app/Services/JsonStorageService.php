<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class JsonStorageService
{
    private string $dataPath = 'data';

    /**
     * Read JSON file
     */
    public function read(string $filename): ?array
    {
        $path = "{$this->dataPath}/{$filename}";

        if (!Storage::exists($path)) {
            Log::warning("JSON file not found: {$path}");
            return null;
        }

        try {
            $contents = Storage::get($path);
            $data = json_decode($contents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("JSON decode error in {$path}: " . json_last_error_msg());
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error("Error reading JSON file {$path}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Write JSON file atomically (with backup)
     */
    public function write(string $filename, array $data): bool
    {
        $path = "{$this->dataPath}/{$filename}";

        try {
            // Create backup if file exists
            if (Storage::exists($path)) {
                $this->backup($filename);
            }

            // Ensure directory exists
            $directory = dirname($path);
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            // Write with pretty print for debugging
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if ($json === false) {
                Log::error("JSON encode error: " . json_last_error_msg());
                return false;
            }

            // Atomic write: write to temp file first, then rename
            $tempPath = "{$path}.tmp";
            Storage::put($tempPath, $json);

            // Rename temp to actual file (atomic on most filesystems)
            $fullTempPath = Storage::path($tempPath);
            $fullPath = Storage::path($path);
            rename($fullTempPath, $fullPath);

            return true;
        } catch (\Exception $e) {
            Log::error("Error writing JSON file {$path}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create backup of JSON file
     */
    public function backup(string $filename): bool
    {
        $path = "{$this->dataPath}/{$filename}";

        if (!Storage::exists($path)) {
            return false;
        }

        try {
            $backupDir = "{$this->dataPath}/backups";

            if (!Storage::exists($backupDir)) {
                Storage::makeDirectory($backupDir);
            }

            $timestamp = now()->format('Y-m-d_His');
            $backupPath = "{$backupDir}/{$timestamp}_{$filename}";

            $contents = Storage::get($path);
            Storage::put($backupPath, $contents);

            Log::info("Backup created: {$backupPath}");

            return true;
        } catch (\Exception $e) {
            Log::error("Error creating backup for {$filename}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all data for initial load
     */
    public function loadAll(): array
    {
        return [
            'employees' => $this->read('employees.json') ?? ['data' => []],
            'jobRoles' => $this->read('job-roles.json') ?? ['data' => []],
            'attendance' => $this->read('attendance-current.json') ?? ['data' => []],
            'dailyStatus' => $this->read('daily-status-current.json') ?? ['data' => []],
            'metadata' => $this->read('metadata.json') ?? [
                'last_updated' => now()->toIso8601String(),
                'version' => 1
            ]
        ];
    }

    /**
     * Save all data (called on app close)
     */
    public function saveAll(array $allData): bool
    {
        $success = true;

        if (isset($allData['employees'])) {
            $success = $success && $this->write('employees.json', $allData['employees']);
        }

        if (isset($allData['jobRoles'])) {
            $success = $success && $this->write('job-roles.json', $allData['jobRoles']);
        }

        if (isset($allData['attendance'])) {
            $success = $success && $this->write('attendance-current.json', $allData['attendance']);
        }

        if (isset($allData['dailyStatus'])) {
            $success = $success && $this->write('daily-status-current.json', $allData['dailyStatus']);
        }

        // Update metadata
        $this->write('metadata.json', [
            'last_updated' => now()->toIso8601String(),
            'version' => $allData['metadata']['version'] ?? 1,
            'last_save_trigger' => $allData['trigger'] ?? 'manual'
        ]);

        return $success;
    }

    /**
     * Clean old backups (keep last 30 days)
     */
    public function cleanOldBackups(int $days = 30): int
    {
        $backupDir = "{$this->dataPath}/backups";

        if (!Storage::exists($backupDir)) {
            return 0;
        }

        $cutoffDate = now()->subDays($days);
        $deleted = 0;

        $files = Storage::files($backupDir);

        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);

            if ($lastModified < $cutoffDate->timestamp) {
                Storage::delete($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            Log::info("Cleaned {$deleted} old backup files");
        }

        return $deleted;
    }
}
