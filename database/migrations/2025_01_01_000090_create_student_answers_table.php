<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('evaluation_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            // selected_option_ids stored as JSON to support multi-answer questions
            $table->json('selected_option_ids');
            $table->boolean('is_correct')->default(false);
            $table->decimal('earned_points', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
