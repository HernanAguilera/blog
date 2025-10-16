<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\Events\CommentRejected;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\Shared\Events\EventDispatcherInterface;

final readonly class BulkRejectCommentsUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    /**
     * Reject multiple comments at once.
     *
     * @param array<string> $commentIds
     * @return array{rejected: int, failed: array<array{id: string, error: string}>}
     */
    public function execute(array $commentIds, int $moderatorId): array
    {
        $rejected = 0;
        $failed = [];
        $moderatorUserId = new UserId($moderatorId);

        foreach ($commentIds as $commentId) {
            try {
                $comment = $this->commentRepository->findById(
                    CommentId::fromString($commentId)
                );

                if (!$comment) {
                    $failed[] = [
                        'id' => $commentId,
                        'error' => 'Comment not found'
                    ];
                    continue;
                }

                // Reject comment
                $comment->reject($moderatorUserId);

                // Save changes
                $this->commentRepository->save($comment);

                // Dispatch event
                $this->eventDispatcher->dispatch(new CommentRejected($comment));

                $rejected++;

            } catch (\DomainException $e) {
                $failed[] = [
                    'id' => $commentId,
                    'error' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $failed[] = [
                    'id' => $commentId,
                    'error' => 'Unexpected error: ' . $e->getMessage()
                ];
            }
        }

        return [
            'rejected' => $rejected,
            'failed' => $failed
        ];
    }
}
