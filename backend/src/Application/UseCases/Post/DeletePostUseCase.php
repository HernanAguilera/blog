<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\Events\PostDeleted;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Shared\Events\EventDispatcherInterface;

final readonly class DeletePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(int $id): bool
    {
        // Find existing post
        $postId = new PostId($id);
        $post = $this->postRepository->findById($postId);

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$id} not found");
        }

        // Delete the post
        $deleted = $this->postRepository->delete($postId);

        if ($deleted) {
            // Dispatch domain event
            $this->eventDispatcher->dispatch(
                new PostDeleted($post->getId(), $post->getTitle(), $post->getAuthorId())
            );
        }

        return $deleted;
    }
}