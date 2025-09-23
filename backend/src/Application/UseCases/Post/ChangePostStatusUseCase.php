<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\ChangePostStatusDTO;
use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\Events\PostStatusChanged;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\PostAccessDeniedException;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostStatus;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\Shared\Events\EventDispatcherInterface;

final readonly class ChangePostStatusUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(ChangePostStatusDTO $dto): void
    {
        $post = $this->postRepository->findById(new PostId($dto->postId));

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$dto->postId} not found");
        }

        $currentUserId = new UserId($dto->userId);

        if (!$post->canBeEditedBy($currentUserId)) {
            throw new PostAccessDeniedException("User {$dto->userId} cannot change status of post {$dto->postId}");
        }

        $oldStatus = $post->getStatus()->value();
        $newStatus = new PostStatus($dto->newStatus);

        // If no change needed, return early
        if ($post->getStatus()->equals($newStatus)) {
            return;
        }

        // Apply the status change using the appropriate method
        match ($dto->newStatus) {
            PostStatus::DRAFT => $post->makeDraft(),
            PostStatus::PUBLISHED => $post->publish(),
            PostStatus::ARCHIVED => $post->archive(),
            PostStatus::SCHEDULED => throw new \InvalidArgumentException(
                'Use SchedulePostUseCase for scheduling posts'
            ),
            default => throw new \InvalidArgumentException("Unknown status: {$dto->newStatus}"),
        };

        $this->postRepository->save($post);

        $this->eventDispatcher->dispatch(
            new PostStatusChanged(
                $post->getId()->value(),
                $post->getTitle()->value(),
                $post->getSlug()->value(),
                $post->getAuthorId()->value(),
                $oldStatus,
                $newStatus->value()
            )
        );
    }
}