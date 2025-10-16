<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\Exceptions\CommentNotFoundException;

final readonly class BulkDeleteCommentsUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {}

    /**
     * Delete multiple comments at once.
     *
     * @param array<string> $commentIds
     * @return array{deleted: int, failed: array<array{id: string, error: string}>}
     */
    public function execute(array $commentIds): array
    {
        $deleted = 0;
        $failed = [];

        foreach ($commentIds as $commentId) {
            try {
                $commentIdVO = CommentId::fromString($commentId);

                // Verify comment exists before deleting
                $comment = $this->commentRepository->findById($commentIdVO);

                if (!$comment) {
                    $failed[] = [
                        'id' => $commentId,
                        'error' => 'Comment not found'
                    ];
                    continue;
                }

                // Delete comment
                $this->commentRepository->delete($commentIdVO);

                $deleted++;

            } catch (CommentNotFoundException $e) {
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
            'deleted' => $deleted,
            'failed' => $failed
        ];
    }
}
