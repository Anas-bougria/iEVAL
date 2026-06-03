<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->text('statement');
            $table->enum('type', ['single', 'multiple'])->default('single')
                  ->comment('single = QCU (une réponse), multiple = QCM (plusieurs)');
            $table->decimal('points', 5, 2)->default(1);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['evaluation_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
