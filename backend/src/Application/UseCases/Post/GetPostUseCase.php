<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostSlug;
use Blog\Domain\Post\Exceptions\PostNotFoundException;

final readonly class GetPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function executeById(int $id): Post
    {
        $postId = new PostId($id);
        $post = $this->postRepository->findById($postId);

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$id} not found");
        }

        return $post;
    }

    public function executeBySlug(string $slug): Post
    {
        $postSlug = new PostSlug($slug);
        $post = $this->postRepository->findBySlug($postSlug);

        if (!$post) {
            throw new PostNotFoundException("Post with slug '{$slug}' not found");
        }

        return $post;
    }
}