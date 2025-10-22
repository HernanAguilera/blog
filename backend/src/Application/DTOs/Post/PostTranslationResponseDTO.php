<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Post;

final class PostTranslationResponseDTO
{
    public function __construct(
        public readonly string $postId,
        public readonly string $locale,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $content,
        public readonly ?string $excerpt,
        public readonly ?string $metaDescription,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}
}
