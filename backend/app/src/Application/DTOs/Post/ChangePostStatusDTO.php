<?php

declare(strict_types=1);

namespace App\src\Application\DTOs\Post;

final readonly class ChangePostStatusDTO
{
    public function __construct(
        public int $postId,
        public int $userId,
        public string $newStatus
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            postId: $data['post_id'],
            userId: $data['user_id'],
            newStatus: $data['new_status']
        );
    }
}