<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Persistence\Eloquent\Repositories;

use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostSlug;
use Blog\Domain\Post\ValueObjects\PostStatus;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Application\DTOs\Post\PostFilterDTO;
use Blog\Infrastructure\Persistence\Eloquent\Models\PostModel;
use Blog\Infrastructure\Persistence\Eloquent\Mappers\PostMapper;
use DateTimeInterface;

final class EloquentPostRepository implements PostRepositoryInterface
{
    public function findById(PostId $id): ?Post
    {
        $model = PostModel::find($id->value());

        return $model ? PostMapper::toDomain($model) : null;
    }

    public function findBySlug(PostSlug $slug): ?Post
    {
        $model = PostModel::where('slug', $slug->value())->first();

        return $model ? PostMapper::toDomain($model) : null;
    }

    public function save(Post $post): Post
    {
        if ($post->getId() === null) {
            // Create new post
            $model = PostMapper::toEloquent($post);
            $model->save();

            // Set the generated ID back to the entity
            $post->setId(new PostId($model->id));

            return $post;
        } else {
            // Update existing post
            $model = PostModel::findOrFail($post->getId()->value());
            $model = PostMapper::updateEloquentFromDomain($model, $post);
            $model->save();

            return PostMapper::toDomain($model);
        }
    }

    public function delete(PostId $id): bool
    {
        return PostModel::destroy($id->value()) > 0;
    }

    public function existsBySlug(PostSlug $slug): bool
    {
        return PostModel::where('slug', $slug->value())->exists();
    }

    public function findWithFilters(PostFilterDTO $filter): array
    {
        $query = PostModel::query();

        // Apply search filter
        if ($filter->search) {
            $query->where(function($q) use ($filter) {
                $q->where('title', 'LIKE', '%' . $filter->search . '%')
                  ->orWhere('content', 'LIKE', '%' . $filter->search . '%')
                  ->orWhere('excerpt', 'LIKE', '%' . $filter->search . '%');
            });
        }

        // Apply status filter
        if ($filter->status) {
            $query->where('status', $filter->status);
        }

        // Apply author filter
        if ($filter->authorId) {
            $query->where('author_id', $filter->authorId);
        }

        // Get total count before pagination
        $total = $query->count();

        // Apply sorting
        $query->orderBy($filter->sortBy, $filter->sortDirection);

        // Apply pagination
        $offset = ($filter->page - 1) * $filter->perPage;
        $models = $query->offset($offset)->limit($filter->perPage)->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $filter->page,
            'perPage' => $filter->perPage
        ];
    }

    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::count();

        $models = PostModel::orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function findByStatus(PostStatus $status, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::where('status', $status->value())->count();

        $models = PostModel::where('status', $status->value())
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function findPublished(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->count();

        $models = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function findDrafts(int $page = 1, int $perPage = 15): array
    {
        return $this->findByStatus(new PostStatus('draft'), $page, $perPage);
    }

    public function findArchived(int $page = 1, int $perPage = 15): array
    {
        return $this->findByStatus(new PostStatus('archived'), $page, $perPage);
    }

    public function findByAuthor(UserId $authorId, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::where('author_id', $authorId->value())->count();

        $models = PostModel::where('author_id', $authorId->value())
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function findByAuthorAndStatus(
        UserId $authorId,
        PostStatus $status,
        int $page = 1,
        int $perPage = 15
    ): array {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::where('author_id', $authorId->value())
            ->where('status', $status->value())
            ->count();

        $models = PostModel::where('author_id', $authorId->value())
            ->where('status', $status->value())
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function search(string $query, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $queryBuilder = PostModel::where(function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('content', 'LIKE', "%{$query}%")
              ->orWhere('excerpt', 'LIKE', "%{$query}%");
        });

        $total = $queryBuilder->count();

        $models = $queryBuilder->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function searchPublished(string $query, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $queryBuilder = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%");
            });

        $total = $queryBuilder->count();

        $models = $queryBuilder->orderBy('published_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function findRecentPublished(int $limit = 10): array
    {
        $models = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return PostMapper::toDomainCollection($models);
    }

    public function findPopular(int $limit = 10): array
    {
        // For now, consider popular as most recently published
        // In the future, this could include views, likes, comments, etc.
        return $this->findRecentPublished($limit);
    }

    public function count(): int
    {
        return PostModel::count();
    }

    public function countByStatus(PostStatus $status): int
    {
        return PostModel::where('status', $status->value())->count();
    }

    public function countPublished(): int
    {
        return PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->count();
    }

    public function countByAuthor(UserId $authorId): int
    {
        return PostModel::where('author_id', $authorId->value())->count();
    }

    public function countByAuthorAndStatus(UserId $authorId, PostStatus $status): int
    {
        return PostModel::where('author_id', $authorId->value())
            ->where('status', $status->value())
            ->count();
    }

    public function findCreatedBetween(
        DateTimeInterface $from,
        DateTimeInterface $to,
        int $page = 1,
        int $perPage = 15
    ): array {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::whereBetween('created_at', [$from, $to])->count();

        $models = PostModel::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function findPublishedBetween(
        DateTimeInterface $from,
        DateTimeInterface $to,
        int $page = 1,
        int $perPage = 15
    ): array {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->whereBetween('published_at', [$from, $to])
            ->count();

        $models = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->whereBetween('published_at', [$from, $to])
            ->orderBy('published_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function findScheduled(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $total = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>', now())
            ->count();

        $models = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>', now())
            ->orderBy('published_at', 'asc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'posts' => PostMapper::toDomainCollection($models),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function nextIdentity(): PostId
    {
        // Generate a temporary ID - the real ID will be set after save
        return new PostId(0);
    }

    public function findReadyToPublish(): array
    {
        $models = PostModel::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        return PostMapper::toDomainCollection($models);
    }

    public function findScheduledReadyToPublish(): array
    {
        $models = PostModel::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        return PostMapper::toDomainCollection($models);
    }

    public function isSlugUniqueForPost(PostSlug $slug, ?PostId $excludePostId = null): bool
    {
        $query = PostModel::where('slug', $slug->value());

        if ($excludePostId !== null) {
            $query->where('id', '!=', $excludePostId->value());
        }

        return !$query->exists();
    }
}