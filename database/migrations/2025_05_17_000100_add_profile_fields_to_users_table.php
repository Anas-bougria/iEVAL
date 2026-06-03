<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Identité — séparée pour permettre l'inscription publique
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');

            // Spécifique aux étudiants
            $table->string('class')->nullable()->after('matricule')
                  ->comment('Classe / filière de l\'étudiant (ex: DAI-2, GE-1)');

            // Contact & infos personnelles
            $table->string('phone', 30)->nullable()->after('class');
            $table->date('birth_date')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'class', 'phone', 'birth_date']);
        });
    }
};
