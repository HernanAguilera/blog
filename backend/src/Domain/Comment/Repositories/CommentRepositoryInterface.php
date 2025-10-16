<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Repositories;

use Blog\Domain\Comment\Entities\Comment;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\ValueObjects\CommentStatus;
use Blog\Domain\Post\ValueObjects\PostId;

interface CommentRepositoryInterface
{
    /**
     * Save a comment (create or update).
     */
    public function save(Comment $comment): void;

    /**
     * Find a comment by their unique identifier.
     */
    public function findById(CommentId $id): ?Comment;

    /**
     * Find comments by post ID with optional status filter.
     *
     * @return Comment[]
     */
    public function findByPostId(PostId $postId, ?CommentStatus $status = null): array;

    /**
     * Find approved comments by post ID.
     *
     * @return Comment[]
     */
    public function findApprovedByPostId(PostId $postId): array;

    /**
     * Find comments pending moderation with pagination.
     *
     * @return Comment[]
     */
    public function findPendingModeration(int $limit = 50, int $offset = 0): array;

    /**
     * Count comments pending moderation.
     */
    public function countPendingModeration(): int;

    /**
     * Find replies to a specific comment.
     *
     * @return Comment[]
     */
    public function findReplies(CommentId $parentId): array;

    /**
     * Delete a comment by their identifier.
     */
    public function delete(CommentId $id): void;

    /**
     * Get complete comment tree for a post.
     * Returns nested array structure with replies already organized.
     *
     * Uses PostgreSQL Recursive CTE for efficient retrieval in a single query.
     * Each node includes a 'depth' field for frontend rendering control.
     *
     * Structure:
     * [
     *   {
     *     "id": "uuid",
     *     "content": "...",
     *     "author": {...},
     *     "depth": 0,
     *     "created_at": "...",
     *     "replies": [
     *       {
     *         "id": "uuid",
     *         "content": "...",
     *         "depth": 1,
     *         "replies": [...]
     *       }
     *     ]
     *   }
     * ]
     *
     * @return array Nested array structure representing the comment tree
     */
    public function getCommentTree(PostId $postId): array;

    /**
     * Count total comments for a post.
     */
    public function countByPostId(PostId $postId): int;

    /**
     * Count approved comments for a post.
     */
    public function countApprovedByPostId(PostId $postId): int;
}
