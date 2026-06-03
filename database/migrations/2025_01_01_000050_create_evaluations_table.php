<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();

            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedInteger('duration_minutes')->default(30);
            $table->unsignedInteger('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->boolean('show_results_immediately')->default(true);

            $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->index();

            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
