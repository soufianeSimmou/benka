<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop tables in reverse order of dependencies
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('daily_attendance_status');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('job_roles');
    }

    public function down(): void
    {
        // Recreate tables if migration is rolled back
        // Not recommended - data will be lost
    }
};
