<?php

declare(strict_types=1);

namespace Blog\Interface\Http\Resources;

use Blog\Domain\Comment\Entities\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Comment $comment */
        $comment = $this->resource;
        $primitives = $comment->toPrimitives();

        return [
            'id' => $primitives['id'],
            'post_id' => $primitives['post_id'],
            'author_type' => $primitives['author_type'],
            'author_name' => $primitives['author_name'] ?? null,
            'author_email' => $primitives['author_email'] ?? null,
            'author_website' => $primitives['author_website'] ?? null,
            'user_id' => $primitives['user_id'],
            'content' => $primitives['content'],
            'status' => $primitives['status'],
            'parent_id' => $primitives['parent_id'],
            'ip_address' => $primitives['ip_address'],
            'user_agent' => $primitives['user_agent'],
            'created_at' => $primitives['created_at'],
            'updated_at' => $primitives['updated_at'],
            'approved_at' => $primitives['approved_at'],
            'approved_by' => $primitives['approved_by'],
        ];
    }
}
