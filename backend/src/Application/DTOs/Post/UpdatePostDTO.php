<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Post;

final readonly class UpdatePostDTO
{
    public function __construct(
        public int $id,
        public ?string $title = null,
        public ?string $content = null,
        public ?string $excerpt = null,
        public ?string $slug = null,
        public ?string $status = null,
        public ?string $featuredImage = null,
        public ?string $metaDescription = null,
        public ?string $scheduledAt = null
    ) {}
}