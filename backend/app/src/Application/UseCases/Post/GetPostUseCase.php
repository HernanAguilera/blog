<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Domain\Post\Entities\Post;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\Post\ValueObjects\PostSlug;
use App\src\Domain\Post\Exceptions\PostNotFoundException;

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