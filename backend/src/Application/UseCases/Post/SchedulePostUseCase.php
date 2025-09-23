<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\SchedulePostDTO;
use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\Events\PostScheduled;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\PostAccessDeniedException;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\Shared\Events\EventDispatcherInterface;
use DateTimeImmutable;

final readonly class SchedulePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(SchedulePostDTO $dto): void
    {
        $post = $this->postRepository->findById(new PostId($dto->postId));

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$dto->postId} not found");
        }

        $currentUserId = new UserId($dto->userId);

        if (!$post->canBeEditedBy($currentUserId)) {
            throw new PostAccessDeniedException("User {$dto->userId} cannot schedule post {$dto->postId}");
        }

        $scheduledAt = new DateTimeImmutable($dto->scheduledAt);

        $post->schedule($scheduledAt);

        $this->postRepository->save($post);

        $this->eventDispatcher->dispatch(
            new PostScheduled(
                $post->getId()->value(),
                $post->getTitle()->value(),
                $post->getSlug()->value(),
                $post->getAuthorId()->value(),
                $scheduledAt
            )
        );
    }
}