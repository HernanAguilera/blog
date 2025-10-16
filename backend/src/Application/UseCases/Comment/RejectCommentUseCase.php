<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\Events\CommentRejected;
use Blog\Domain\Comment\Exceptions\CommentNotFoundException;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\Shared\Events\EventDispatcherInterface;

final readonly class RejectCommentUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(string $commentId, int $moderatorId): void
    {
        $comment = $this->commentRepository->findById(CommentId::fromString($commentId));

        if (!$comment) {
            throw CommentNotFoundException::withId(CommentId::fromString($commentId));
        }

        // Reject comment
        $comment->reject(new UserId($moderatorId));

        // Save changes
        $this->commentRepository->save($comment);

        // Dispatch event
        $this->eventDispatcher->dispatch(new CommentRejected($comment));
    }
}
