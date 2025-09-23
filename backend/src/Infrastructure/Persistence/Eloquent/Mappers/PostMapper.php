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
        return Post::fromPrimitives(
            id: $model->id,
            title: $model->title,
            slug: $model->slug,
            content: $model->content,
            status: $model->status,
            authorId: $model->author_id,
            metaDescription: $model->meta_description,
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

        $model->title = $primitives['title'];
        $model->slug = $primitives['slug'];
        $model->content = $primitives['content'];
        $model->status = $primitives['status'];
        $model->author_id = $primitives['author_id'];
        $model->meta_description = $primitives['meta_description'];
        $model->reading_time = $primitives['reading_time'];
        $model->published_at = $primitives['published_at'];
        $model->scheduled_at = $primitives['scheduled_at'];

        return $model;
    }

    public static function updateEloquentFromDomain(PostModel $model, Post $post): PostModel
    {
        $primitives = $post->toPrimitives();

        $model->title = $primitives['title'];
        $model->slug = $primitives['slug'];
        $model->content = $primitives['content'];
        $model->status = $primitives['status'];
        $model->author_id = $primitives['author_id'];
        $model->meta_description = $primitives['meta_description'];
        $model->reading_time = $primitives['reading_time'];
        $model->published_at = $primitives['published_at'];
        $model->scheduled_at = $primitives['scheduled_at'];

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