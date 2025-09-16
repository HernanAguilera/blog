<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\Post\Events\PostDeleted;
use App\src\Domain\Post\Exceptions\PostNotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;

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