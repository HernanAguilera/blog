<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Domain\Post\Entities\Post;
use App\src\Domain\Post\Events\PostArchived;
use App\src\Domain\Post\Exceptions\PostNotFoundException;
use App\src\Domain\Post\Exceptions\PostAccessDeniedException;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\User\ValueObjects\UserId;
use App\src\Domain\Shared\Events\EventDispatcherInterface;

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