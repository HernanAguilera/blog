<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Eliminar campos traducibles de la tabla posts.
     * Estos campos ahora viven exclusivamente en post_translations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Eliminar índice de slug primero (si existe)
            $table->dropUnique(['slug']);

            // Eliminar columnas traducibles
            $table->dropColumn([
                'title',
                'slug',
                'content',
                'excerpt',
                'meta_description'
            ]);
        });

        echo "✅ Columnas traducibles eliminadas de posts\n";
    }

    /**
     * Reverse the migrations.
     *
     * Restaurar columnas en caso de rollback.
     * IMPORTANTE: Los datos NO se restaurarán, solo la estructura.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Restaurar columnas
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->text('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('meta_description', 160)->nullable();

            // Restaurar índice único en slug
            $table->unique('slug');
        });

        echo "⚠️  Columnas restauradas en posts (sin datos)\n";
    }
};
