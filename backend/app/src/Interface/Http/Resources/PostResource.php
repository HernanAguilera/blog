<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Resources;

use App\src\Domain\Post\Entities\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Post $post */
        $post = $this->resource;
        $primitives = $post->toPrimitives();

        return [
            'id' => $primitives['id'],
            'title' => $primitives['title'],
            'slug' => $primitives['slug'],
            'content' => $primitives['content'],
            'excerpt' => null, // TODO: Implement excerpt functionality
            'status' => $primitives['status'],
            'author_id' => $primitives['author_id'],
            'meta_description' => $primitives['meta_description'],
            'reading_time' => $primitives['reading_time'],
            'word_count' => $post->getWordCount(),
            'is_published' => $post->isPublished(),
            'is_draft' => $post->isDraft(),
            'is_archived' => $post->isArchived(),
            'created_at' => $primitives['created_at'],
            'updated_at' => $primitives['updated_at'],
            'published_at' => $primitives['published_at'],
        ];
    }
}