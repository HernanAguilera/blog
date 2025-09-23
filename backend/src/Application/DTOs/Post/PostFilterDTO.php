<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Post;

final readonly class PostFilterDTO
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
        public ?string $status = null,
        public ?int $authorId = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc'
    ) {}
}