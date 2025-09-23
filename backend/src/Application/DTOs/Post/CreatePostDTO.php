<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Post;

final readonly class CreatePostDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public string $status = 'draft',
        public ?string $featuredImage = null,
        public ?string $metaDescription = null,
        public int $authorId,
        public ?string $scheduledAt = null
    ) {}
}