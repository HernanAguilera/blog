<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Repositories;

use App\src\Domain\Post\Entities\Post;
use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\Post\ValueObjects\PostSlug;
use App\src\Domain\Post\ValueObjects\PostStatus;
use App\src\Domain\User\ValueObjects\UserId;

interface PostRepositoryInterface
{
    /**
     * Find a post by their unique identifier.
     */
    public function findById(PostId $id): ?Post;

    /**
     * Find a post by their slug.
     */
    public function findBySlug(PostSlug $slug): ?Post;

    /**
     * Save a post (create or update).
     */
    public function save(Post $post): Post;

    /**
     * Delete a post by their identifier.
     */
    public function delete(PostId $id): bool;

    /**
     * Check if a post exists by slug.
     */
    public function existsBySlug(PostSlug $slug): bool;

    /**
     * Find all posts with pagination.
     *
     * @return array{posts: Post[], total: int, page: int, perPage: int}
     */
    public function findAll(int $page = 1, int $perPage = 15): array;

    /**
     * Find posts by status.
     *
     * @return Post[]
     */
    public function findByStatus(PostStatus $status, int $page = 1, int $perPage = 15): array;

    /**
     * Find published posts only.
     *
     * @return Post[]
     */
    public function findPublished(int $page = 1, int $perPage = 15): array;

    /**
     * Find draft posts only.
     *
     * @return Post[]
     */
    public function findDrafts(int $page = 1, int $perPage = 15): array;

    /**
     * Find archived posts only.
     *
     * @return Post[]
     */
    public function findArchived(int $page = 1, int $perPage = 15): array;

    /**
     * Find posts by author.
     *
     * @return Post[]
     */
    public function findByAuthor(UserId $authorId, int $page = 1, int $perPage = 15): array;

    /**
     * Find posts by author and status.
     *
     * @return Post[]
     */
    public function findByAuthorAndStatus(
        UserId $authorId,
        PostStatus $status,
        int $page = 1,
        int $perPage = 15
    ): array;

    /**
     * Search posts by title or content.
     *
     * @return Post[]
     */
    public function search(string $query, int $page = 1, int $perPage = 15): array;

    /**
     * Search published posts only.
     *
     * @return Post[]
     */
    public function searchPublished(string $query, int $page = 1, int $perPage = 15): array;

    /**
     * Find recent published posts.
     *
     * @return Post[]
     */
    public function findRecentPublished(int $limit = 10): array;

    /**
     * Find popular posts (by reading time or other criteria).
     *
     * @return Post[]
     */
    public function findPopular(int $limit = 10): array;

    /**
     * Count total posts.
     */
    public function count(): int;

    /**
     * Count posts by status.
     */
    public function countByStatus(PostStatus $status): int;

    /**
     * Count published posts.
     */
    public function countPublished(): int;

    /**
     * Count posts by author.
     */
    public function countByAuthor(UserId $authorId): int;

    /**
     * Count posts by author and status.
     */
    public function countByAuthorAndStatus(UserId $authorId, PostStatus $status): int;

    /**
     * Get posts created in a date range.
     *
     * @return Post[]
     */
    public function findCreatedBetween(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $page = 1,
        int $perPage = 15
    ): array;

    /**
     * Get posts published in a date range.
     *
     * @return Post[]
     */
    public function findPublishedBetween(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $page = 1,
        int $perPage = 15
    ): array;

    /**
     * Find posts scheduled for publishing (future published_at dates).
     *
     * @return Post[]
     */
    public function findScheduled(int $page = 1, int $perPage = 15): array;

    /**
     * Get the next available post ID.
     */
    public function nextIdentity(): PostId;

    /**
     * Find posts that need to be published (scheduled posts with published_at <= now).
     *
     * @return Post[]
     */
    public function findReadyToPublish(): array;

    /**
     * Check if slug is unique for a specific post (excluding the post itself).
     */
    public function isSlugUniqueForPost(PostSlug $slug, ?PostId $excludePostId = null): bool;
}