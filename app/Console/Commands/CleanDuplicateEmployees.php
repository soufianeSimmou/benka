<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:clean-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate employee records from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Searching for duplicate employees...');

        // Find duplicates based on first_name + last_name
        $duplicates = Employee::whereNull('deleted_at')
            ->selectRaw('first_name, last_name, COUNT(*) as count')
            ->groupBy('first_name', 'last_name')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate employees found!');
            return 0;
        }

        $this->warn("Found {$duplicates->count()} groups of duplicate employees:");

        $totalDeleted = 0;
        $duplicateGroups = [];

        // Process each group of duplicates
        foreach ($duplicates as $dup) {
            $employees = Employee::whereNull('deleted_at')
                ->where('first_name', $dup->first_name)
                ->where('last_name', $dup->last_name)
                ->orderBy('id', 'desc')
                ->get();

            $duplicateGroups[] = [
                'name' => "{$dup->first_name} {$dup->last_name}",
                'count' => $employees->count(),
                'keep_id' => $employees->first()->id,
                'delete_ids' => $employees->skip(1)->pluck('id')->toArray()
            ];
        }

        // Show what will be done
        $this->table(
            ['Employee Name', 'Total Found', 'Keep ID', 'Delete IDs'],
            collect($duplicateGroups)->map(fn($g) => [
                $g['name'],
                $g['count'],
                $g['keep_id'],
                implode(', ', $g['delete_ids'])
            ])
        );

        // Ask for confirmation
        if (!$this->confirm('Do you want to proceed with soft-deleting these duplicates?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Perform the cleanup
        DB::beginTransaction();

        try {
            foreach ($duplicateGroups as $group) {
                foreach ($group['delete_ids'] as $deleteId) {
                    $employee = Employee::find($deleteId);
                    $employee->delete(); // Soft delete
                    $totalDeleted++;

                    $this->line("  🗑️  Deleted: {$group['name']} (ID: {$deleteId})");
                }

                $this->info("  ✅ Kept: {$group['name']} (ID: {$group['keep_id']})");
            }

            DB::commit();

            $this->newLine();
            $this->info("✅ Successfully cleaned {$totalDeleted} duplicate employee(s)!");
            $this->info("📝 Duplicates were soft-deleted and can be recovered if needed.");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("❌ Error during cleanup: {$e->getMessage()}");
            $this->error("Transaction rolled back. No changes were made.");

            return 1;
        }
    }
}
