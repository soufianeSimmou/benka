<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('daily_attendance_status');
    }
};
