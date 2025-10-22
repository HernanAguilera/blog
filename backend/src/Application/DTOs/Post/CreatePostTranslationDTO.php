<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Post;

final class CreatePostTranslationDTO
{
    public function __construct(
        public readonly string $postId,
        public readonly string $locale,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $content,
        public readonly ?string $excerpt = null,
        public readonly ?string $metaDescription = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            postId: $data['post_id'],
            locale: $data['locale'],
            title: $data['title'],
            slug: $data['slug'],
            content: $data['content'],
            excerpt: $data['excerpt'] ?? null,
            metaDescription: $data['meta_description'] ?? null
        );
    }
}
