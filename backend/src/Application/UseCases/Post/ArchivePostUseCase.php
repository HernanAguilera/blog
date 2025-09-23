<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\Events\PostArchived;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\PostAccessDeniedException;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\Shared\Events\EventDispatcherInterface;

final readonly class ArchivePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(int $postId, int $userId): void
    {
        $post = $this->postRepository->findById(new PostId($postId));

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$postId} not found");
        }

        $currentUserId = new UserId($userId);

        if (!$post->canBeEditedBy($currentUserId)) {
            throw new PostAccessDeniedException("User {$userId} cannot archive post {$postId}");
        }

        if ($post->isArchived()) {
            return; // Already archived, nothing to do
        }

        $post->archive();

        $this->postRepository->save($post);

        $this->eventDispatcher->dispatch(
            new PostArchived(
                $post->getId()->value(),
                $post->getTitle()->value(),
                $post->getSlug()->value(),
                $post->getAuthorId()->value()
            )
        );
    }
}