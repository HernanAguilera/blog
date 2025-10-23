<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Persistence\Eloquent\Mappers;

use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Infrastructure\Persistence\Eloquent\Models\PostModel;

final class PostMapper
{
    public static function toDomain(PostModel $model): Post
    {
        // Obtener traducción por defecto
        $defaultLocale = config('locales.default', 'es');
        $translation = $model->translations->firstWhere('locale', $defaultLocale);

        if (!$translation) {
            throw new \RuntimeException(
                "Post {$model->id} does not have a default translation (locale: {$defaultLocale})"
            );
        }

        return Post::fromPrimitives(
            id: $model->id,
            title: $translation->title,
            slug: $translation->slug,
            content: $translation->content,
            status: $model->status,
            authorId: $model->author_id,
            metaDescription: $translation->meta_description,
            createdAt: $model->created_at?->format('Y-m-d H:i:s'),
            updatedAt: $model->updated_at?->format('Y-m-d H:i:s'),
            publishedAt: $model->published_at?->format('Y-m-d H:i:s'),
            scheduledAt: $model->scheduled_at?->format('Y-m-d H:i:s')
        );
    }

    public static function toEloquent(Post $post): PostModel
    {
        $primitives = $post->toPrimitives();

        $model = new PostModel();

        // Set ID only if exists (for updates)
        if ($primitives['id'] !== null) {
            $model->id = $primitives['id'];
            $model->exists = true;
        }

        // Solo campos NO traducibles
        $model->status = $primitives['status'];
        $model->author_id = $primitives['author_id'];
        $model->reading_time = $primitives['reading_time'];
        $model->published_at = $primitives['published_at'];
        $model->scheduled_at = $primitives['scheduled_at'];

        // Los campos traducibles (title, slug, content, meta_description)
        // se manejan en post_translations, no aquí

        return $model;
    }

    public static function updateEloquentFromDomain(PostModel $model, Post $post): PostModel
    {
        $primitives = $post->toPrimitives();

        // Solo actualizar campos NO traducibles
        $model->status = $primitives['status'];
        $model->author_id = $primitives['author_id'];
        $model->reading_time = $primitives['reading_time'];
        $model->published_at = $primitives['published_at'];
        $model->scheduled_at = $primitives['scheduled_at'];

        // Los campos traducibles (title, slug, content, meta_description)
        // se manejan en post_translations, no aquí

        return $model;
    }

    /**
     * @param PostModel[]|\Illuminate\Database\Eloquent\Collection $models
     * @return Post[]
     */
    public static function toDomainCollection($models): array
    {
        if ($models instanceof \Illuminate\Database\Eloquent\Collection) {
            return $models->map(fn(PostModel $model) => self::toDomain($model))->toArray();
        }

        return array_map(
            fn(PostModel $model) => self::toDomain($model),
            $models
        );
    }
}