<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\JobRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanInvalidRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clean-invalid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean invalid records with empty names from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Searching for invalid records...');

        DB::beginTransaction();

        try {
            // Find and delete job roles with empty names
            $invalidJobRoles = JobRole::where(function($query) {
                $query->whereNull('name')
                      ->orWhere('name', '')
                      ->orWhereRaw('TRIM(name) = ?', ['']);
            })->get();

            if ($invalidJobRoles->count() > 0) {
                $this->warn("Found {$invalidJobRoles->count()} invalid job role(s):");
                foreach ($invalidJobRoles as $job) {
                    $this->line("  - ID: {$job->id}, Name: '" . ($job->name ?? 'NULL') . "'");
                }

                foreach ($invalidJobRoles as $job) {
                    $job->forceDelete(); // Permanent delete since it's invalid data
                }

                $this->info("✅ Deleted {$invalidJobRoles->count()} invalid job role(s)");
            } else {
                $this->info('✅ No invalid job roles found');
            }

            // Find and delete employees with empty names
            $invalidEmployees = Employee::whereNull('deleted_at')
                ->where(function($query) {
                    $query->whereNull('first_name')
                          ->orWhere('first_name', '')
                          ->orWhereRaw('TRIM(first_name) = ?', [''])
                          ->orWhereNull('last_name')
                          ->orWhere('last_name', '')
                          ->orWhereRaw('TRIM(last_name) = ?', ['']);
                })->get();

            if ($invalidEmployees->count() > 0) {
                $this->warn("Found {$invalidEmployees->count()} invalid employee(s):");
                foreach ($invalidEmployees as $emp) {
                    $this->line("  - ID: {$emp->id}, Name: '" . ($emp->first_name ?? 'NULL') . " " . ($emp->last_name ?? 'NULL') . "'");
                }

                foreach ($invalidEmployees as $emp) {
                    $emp->delete(); // Soft delete
                }

                $this->info("✅ Soft-deleted {$invalidEmployees->count()} invalid employee(s)");
            } else {
                $this->info('✅ No invalid employees found');
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ Database cleanup completed successfully!');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("❌ Error during cleanup: {$e->getMessage()}");
            $this->error("Transaction rolled back. No changes were made.");

            return 1;
        }
    }
}
