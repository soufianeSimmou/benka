<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_roles', function (Blueprint $table) {
            $table->decimal('daily_salary', 10, 2)->nullable()->after('description');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('daily_salary');
        });
    }

    public function down(): void
    {
        Schema::table('job_roles', function (Blueprint $table) {
            $table->dropColumn(['daily_salary', 'hourly_rate']);
        });
    }
};
