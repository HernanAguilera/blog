<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\Exceptions\CommentNotFoundException;

final readonly class DeleteCommentUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {}

    public function execute(string $commentId): void
    {
        $comment = $this->commentRepository->findById(CommentId::fromString($commentId));

        if (!$comment) {
            throw CommentNotFoundException::withId(CommentId::fromString($commentId));
        }

        $this->commentRepository->delete(CommentId::fromString($commentId));
    }
}
