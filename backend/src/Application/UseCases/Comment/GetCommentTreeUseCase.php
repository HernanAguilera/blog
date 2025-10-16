<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;

final readonly class GetCommentTreeUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {}

    /**
     * Get nested comment tree for a post.
     * Returns array with structure: [{id, content, author, depth, replies: [...]}]
     */
    public function execute(int $postId): array
    {
        return $this->commentRepository->getCommentTree(new PostId($postId));
    }
}
