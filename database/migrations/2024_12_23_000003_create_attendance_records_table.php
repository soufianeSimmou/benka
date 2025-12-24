<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent'])->default('absent');
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('marked_at')->nullable();
            $table->timestamps();

            // Un seul enregistrement par employé/jour
            $table->unique(['employee_id', 'date']);

            // Indexes
            $table->index('date');
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
