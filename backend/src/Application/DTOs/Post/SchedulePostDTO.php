<?php

declare(strict_types=1);

namespace Blog\Application\DTOs\Post;

final readonly class SchedulePostDTO
{
    public function __construct(
        public int $postId,
        public int $userId,
        public string $scheduledAt
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            postId: $data['post_id'],
            userId: $data['user_id'],
            scheduledAt: $data['scheduled_at']
        );
    }
}