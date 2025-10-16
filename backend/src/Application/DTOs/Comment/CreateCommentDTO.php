<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Comment;

final readonly class CreateCommentDTO
{
    public function __construct(
        public int $postId,
        public int $userId,
        public string $content,
        public ?string $parentId = null,
        public string $ipAddress,
        public string $userAgent
    ) {}
}
