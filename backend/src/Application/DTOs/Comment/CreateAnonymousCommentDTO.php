<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Comment;

final readonly class CreateAnonymousCommentDTO
{
    public function __construct(
        public int $postId,
        public string $authorName,
        public string $authorEmail,
        public ?string $authorWebsite,
        public string $content,
        public ?string $parentId = null,
        public string $ipAddress,
        public string $userAgent,
        public string $turnstileToken
    ) {}
}
