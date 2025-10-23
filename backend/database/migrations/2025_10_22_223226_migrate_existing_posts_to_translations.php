<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migrar todos los posts existentes a post_translations
     * creando una traducción en español (idioma por defecto) para cada uno.
     */
    public function up(): void
    {
        // Obtener todos los posts con sus campos traducibles
        $posts = DB::table('posts')
            ->select('id', 'title', 'slug', 'content', 'excerpt', 'meta_description', 'created_at', 'updated_at')
            ->get();

        $defaultLocale = config('locales.default', 'es');

        foreach ($posts as $post) {
            // Verificar si ya existe una traducción para este post
            $existingTranslation = DB::table('post_translations')
                ->where('post_id', $post->id)
                ->where('locale', $defaultLocale)
                ->exists();

            // Solo crear si no existe (idempotencia)
            if (!$existingTranslation) {
                DB::table('post_translations')->insert([
                    'post_id' => $post->id,
                    'locale' => $defaultLocale,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'content' => $post->content,
                    'excerpt' => $post->excerpt,
                    'meta_description' => $post->meta_description,
                    'created_at' => $post->created_at ?? now(),
                    'updated_at' => $post->updated_at ?? now(),
                ]);
            }
        }

        // Log del resultado
        $totalPosts = $posts->count();
        $totalTranslations = DB::table('post_translations')
            ->where('locale', $defaultLocale)
            ->count();

        echo "✅ Migración completada: {$totalPosts} posts → {$totalTranslations} traducciones\n";
    }

    /**
     * Reverse the migrations.
     *
     * Eliminar las traducciones creadas por esta migración
     */
    public function down(): void
    {
        $defaultLocale = config('locales.default', 'es');

        // Eliminar solo las traducciones en el idioma por defecto
        // que se crearon durante esta migración
        DB::table('post_translations')
            ->where('locale', $defaultLocale)
            ->delete();

        echo "⚠️  Rollback completado: traducciones en '{$defaultLocale}' eliminadas\n";
    }
};
