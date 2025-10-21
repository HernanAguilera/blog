<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Paso 1: Eliminar el constraint viejo
        DB::statement("ALTER TABLE comments DROP CONSTRAINT comments_status_check");

        // Paso 2: Actualizar valores existentes ANTES de agregar el nuevo constraint
        DB::table('comments')->where('status', 'pending_approval')->update(['status' => 'pending']);

        // Paso 3: Agregar el nuevo constraint
        DB::statement("ALTER TABLE comments ADD CONSTRAINT comments_status_check CHECK (status IN ('pending', 'approved', 'rejected', 'spam'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios
        DB::table('comments')->where('status', 'pending')->update(['status' => 'pending_approval']);

        DB::statement("ALTER TABLE comments DROP CONSTRAINT comments_status_check");
        DB::statement("ALTER TABLE comments ADD CONSTRAINT comments_status_check CHECK (status IN ('pending_approval', 'approved', 'rejected', 'spam'))");
    }
};
