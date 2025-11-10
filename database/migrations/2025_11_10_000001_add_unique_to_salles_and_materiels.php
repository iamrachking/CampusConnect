<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('salles') && !Schema::hasColumn('salles', 'nom_salle')) {
            // Skip if structure unexpected
        }
        if (Schema::hasTable('materiels') && !Schema::hasColumn('materiels', 'nom_materiel')) {
            // Skip if structure unexpected
        }

        Schema::table('salles', function (Blueprint $table) {
            try { $table->unique('nom_salle'); } catch (\Throwable $e) { /* ignore if already unique */ }
        });
        Schema::table('materiels', function (Blueprint $table) {
            try { $table->unique('nom_materiel'); } catch (\Throwable $e) { /* ignore if already unique */ }
        });
    }

    public function down(): void
    {
        Schema::table('salles', function (Blueprint $table) {
            try { $table->dropUnique(['nom_salle']); } catch (\Throwable $e) { /* ignore */ }
        });
        Schema::table('materiels', function (Blueprint $table) {
            try { $table->dropUnique(['nom_materiel']); } catch (\Throwable $e) { /* ignore */ }
        });
    }
};