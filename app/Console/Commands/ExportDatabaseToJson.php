<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\JobRole;
use App\Models\AttendanceRecord;
use App\Models\DailyAttendanceStatus;
use App\Services\JsonStorageService;
use Illuminate\Console\Command;

class ExportDatabaseToJson extends Command
{
    protected $signature = 'db:export-json {--backup : Create backup before export}';
    protected $description = 'Export all database data to JSON files';

    private JsonStorageService $jsonStorage;

    public function __construct(JsonStorageService $jsonStorage)
    {
        parent::__construct();
        $this->jsonStorage = $jsonStorage;
    }

    public function handle(): int
    {
        $this->info('🚀 Starting database export to JSON...');

        if ($this->option('backup')) {
            $this->info('📦 Creating backups of existing JSON files...');
            $this->jsonStorage->backup('employees.json');
            $this->jsonStorage->backup('job-roles.json');
            $this->jsonStorage->backup('attendance-current.json');
            $this->jsonStorage->backup('daily-status-current.json');
        }

        // Export Job Roles
        $this->exportJobRoles();

        // Export Employees
        $this->exportEmployees();

        // Export Attendance Records
        $this->exportAttendance();

        // Export Daily Status
        $this->exportDailyStatus();

        // Create metadata
        $this->createMetadata();

        $this->newLine();
        $this->info('✅ Database export completed successfully!');
        $this->info('📂 Files saved in: storage/app/data/');

        return Command::SUCCESS;
    }

    private function exportJobRoles(): void
    {
        $this->info('👷 Exporting job roles...');

        $jobRoles = JobRole::orderBy('display_order')->get();

        $data = [
            'version' => 1,
            'last_updated' => now()->toIso8601String(),
            'count' => $jobRoles->count(),
            'data' => $jobRoles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'daily_salary' => (float) $role->daily_salary,
                    'hourly_rate' => (float) $role->hourly_rate,
                    'display_order' => $role->display_order,
                    'created_at' => $role->created_at?->toIso8601String(),
                    'updated_at' => $role->updated_at?->toIso8601String(),
                ];
            })->values()->toArray()
        ];

        $this->jsonStorage->write('job-roles.json', $data);
        $this->line("  ✓ Exported {$data['count']} job roles");
    }

    private function exportEmployees(): void
    {
        $this->info('👥 Exporting employees...');

        // Include soft-deleted employees for historical data
        $employees = Employee::withTrashed()
            ->with('jobRole')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $data = [
            'version' => 1,
            'last_updated' => now()->toIso8601String(),
            'count' => $employees->count(),
            'data' => $employees->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'first_name' => $emp->first_name,
                    'last_name' => $emp->last_name,
                    'job_role_id' => $emp->job_role_id,
                    'job_role_name' => $emp->jobRole?->name,
                    'phone' => $emp->phone,
                    'is_active' => (bool) $emp->is_active,
                    'created_at' => $emp->created_at?->toIso8601String(),
                    'updated_at' => $emp->updated_at?->toIso8601String(),
                    'deleted_at' => $emp->deleted_at?->toIso8601String(),
                ];
            })->values()->toArray()
        ];

        $this->jsonStorage->write('employees.json', $data);
        $this->line("  ✓ Exported {$data['count']} employees");
    }

    private function exportAttendance(): void
    {
        $this->info('📅 Exporting attendance records...');

        $records = AttendanceRecord::with('employee')
            ->orderBy('date', 'desc')
            ->orderBy('employee_id')
            ->get();

        $data = [
            'version' => 1,
            'last_updated' => now()->toIso8601String(),
            'count' => $records->count(),
            'data' => $records->map(function ($record) {
                return [
                    'id' => $record->id,
                    'employee_id' => $record->employee_id,
                    'employee_name' => $record->employee ?
                        "{$record->employee->first_name} {$record->employee->last_name}" : null,
                    'date' => $record->date,
                    'status' => $record->status,
                    'marked_by' => $record->marked_by,
                    'marked_at' => $record->marked_at?->toIso8601String(),
                    'created_at' => $record->created_at?->toIso8601String(),
                    'updated_at' => $record->updated_at?->toIso8601String(),
                ];
            })->values()->toArray()
        ];

        $this->jsonStorage->write('attendance-current.json', $data);
        $this->line("  ✓ Exported {$data['count']} attendance records");
    }

    private function exportDailyStatus(): void
    {
        $this->info('🔒 Exporting daily attendance status...');

        $statuses = DailyAttendanceStatus::orderBy('date', 'desc')->get();

        $data = [
            'version' => 1,
            'last_updated' => now()->toIso8601String(),
            'count' => $statuses->count(),
            'data' => $statuses->map(function ($status) {
                return [
                    'id' => $status->id,
                    'date' => $status->date,
                    'is_completed' => (bool) $status->is_completed,
                    'completed_by' => $status->completed_by,
                    'completed_at' => $status->completed_at?->toIso8601String(),
                    'created_at' => $status->created_at?->toIso8601String(),
                    'updated_at' => $status->updated_at?->toIso8601String(),
                ];
            })->values()->toArray()
        ];

        $this->jsonStorage->write('daily-status-current.json', $data);
        $this->line("  ✓ Exported {$data['count']} daily status records");
    }

    private function createMetadata(): void
    {
        $this->info('📝 Creating metadata...');

        $metadata = [
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'database' => config('database.default'),
            'app_version' => config('app.version', '1.0.0'),
            'counts' => [
                'employees' => Employee::withTrashed()->count(),
                'job_roles' => JobRole::count(),
                'attendance_records' => AttendanceRecord::count(),
                'daily_statuses' => DailyAttendanceStatus::count(),
            ],
            'date_range' => [
                'first_attendance' => AttendanceRecord::min('date'),
                'last_attendance' => AttendanceRecord::max('date'),
            ]
        ];

        $this->jsonStorage->write('metadata.json', $metadata);
        $this->line('  ✓ Metadata created');
    }
}
