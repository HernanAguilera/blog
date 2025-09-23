<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\PostAccessDeniedException;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\User\ValueObjects\UserId;

final readonly class GetPostTransitionsUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
    }

    /**
     * Get available status transitions for a post.
     *
     * @return array{current_status: string, available_transitions: array<string>, can_schedule: bool}
     */
    public function execute(int $postId, int $userId): array
    {
        $post = $this->postRepository->findById(new PostId($postId));

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$postId} not found");
        }

        $currentUserId = new UserId($userId);

        if (!$post->canBeEditedBy($currentUserId)) {
            throw new PostAccessDeniedException("User {$userId} cannot view transitions for post {$postId}");
        }

        $availableTransitions = $post->getAvailableTransitions();
        $currentStatus = $post->getStatus()->value();

        return [
            'current_status' => $currentStatus,
            'available_transitions' => $availableTransitions,
            'can_schedule' => in_array('scheduled', $availableTransitions, true),
            'is_ready_to_publish' => $post->isReadyToPublish(),
            'scheduled_at' => $post->getScheduledAt()?->format('Y-m-d H:i:s'),
            'published_at' => $post->getPublishedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}