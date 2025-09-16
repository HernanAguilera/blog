<?php

declare(strict_types=1);

namespace App\src\Application\DTOs\Post;

final readonly class PreviewPostDTO
{
    public function __construct(
        public int $userId,
        public string $title,
        public string $content,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public string $status = 'draft',
        public ?string $featuredImage = null,
        public ?string $metaDescription = null,
        public ?string $scheduledAt = null,
        public int $ttlHours = 24
    ) {}
}