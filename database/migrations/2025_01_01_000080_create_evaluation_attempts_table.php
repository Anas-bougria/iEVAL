<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluation_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 6, 2)->nullable();         // points obtenus
            $table->decimal('max_score', 6, 2)->nullable();     // total possible
            $table->decimal('grade_20', 5, 2)->nullable();      // note sur 20
            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted', 'cancelled'])
                  ->default('in_progress')->index();
            $table->timestamps();

            $table->index(['evaluation_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_attempts');
    }
};
