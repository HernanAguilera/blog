<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;

final readonly class GetPendingCommentsUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {}

    /**
     * Get comments pending moderation with pagination.
     *
     * @return array{comments: Comment[], total: int}
     */
    public function execute(int $limit = 50, int $offset = 0): array
    {
        $comments = $this->commentRepository->findPendingModeration($limit, $offset);
        $total = $this->commentRepository->countPendingModeration();

        return [
            'comments' => $comments,
            'total' => $total,
        ];
    }
}
