<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Drop all tables except auth-related ones
     * The app now uses JSON storage for all data except authentication
     */
    public function up(): void
    {
        // Drop data tables (now using JSON storage)
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('daily_attendance_status');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('job_roles');

        // Keep these tables for authentication and Laravel functionality:
        // - users (Google OAuth authentication)
        // - password_reset_tokens
        // - sessions
        // - cache, cache_locks
        // - jobs, job_batches, failed_jobs
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Recreate job_roles table
        Schema::create('job_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->decimal('daily_salary', 10, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index('display_order');
        });

        // Recreate employees table
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('job_role_id')->constrained('job_roles')->onDelete('restrict');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['last_name', 'first_name']);
            $table->index('is_active');
        });

        // Recreate attendance_records table
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent'])->default('absent');
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('marked_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->index('date');
            $table->index(['date', 'status']);
        });

        // Recreate daily_attendance_status table
        Schema::create('daily_attendance_status', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->boolean('is_completed')->default(false);
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('date');
            $table->index(['date', 'is_completed']);
        });
    }
};
