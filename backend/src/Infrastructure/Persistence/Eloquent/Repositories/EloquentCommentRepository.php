<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Persistence\Eloquent\Repositories;

use Blog\Domain\Comment\Entities\Comment;
use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\ValueObjects\CommentStatus;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Infrastructure\Persistence\Eloquent\Models\CommentModel;
use Blog\Infrastructure\Persistence\Eloquent\Mappers\CommentMapper;
use Illuminate\Support\Facades\DB;

final class EloquentCommentRepository implements CommentRepositoryInterface
{
    public function save(Comment $comment): void
    {
        if ($comment->id() === null) {
            // Create new comment
            $model = CommentMapper::toEloquent($comment);
            $model->save();
        } else {
            // Update existing comment
            $model = CommentModel::findOrFail($comment->id()->value());
            $model = CommentMapper::updateEloquentFromDomain($model, $comment);
            $model->save();
        }
    }

    public function findById(CommentId $id): ?Comment
    {
        $model = CommentModel::find($id->value());

        return $model ? CommentMapper::toDomain($model) : null;
    }

    public function findByPostId(PostId $postId, ?CommentStatus $status = null): array
    {
        $query = CommentModel::where('post_id', $postId->value());

        if ($status !== null) {
            $query->where('status', $status->value());
        }

        $models = $query->orderBy('created_at', 'asc')->get();

        return CommentMapper::toDomainCollection($models);
    }

    public function findApprovedByPostId(PostId $postId): array
    {
        $models = CommentModel::where('post_id', $postId->value())
            ->where('status', 'approved')
            ->orderBy('created_at', 'asc')
            ->get();

        return CommentMapper::toDomainCollection($models);
    }

    public function findPendingModeration(int $limit = 50, int $offset = 0): array
    {
        $models = CommentModel::where('status', 'pending_approval')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return CommentMapper::toDomainCollection($models);
    }

    public function countPendingModeration(): int
    {
        return CommentModel::where('status', 'pending_approval')->count();
    }

    public function findReplies(CommentId $parentId): array
    {
        $models = CommentModel::where('parent_id', $parentId->value())
            ->orderBy('created_at', 'asc')
            ->get();

        return CommentMapper::toDomainCollection($models);
    }

    public function delete(CommentId $id): void
    {
        CommentModel::destroy($id->value());
    }

    public function getCommentTree(PostId $postId): array
    {
        // Recursive CTE query for PostgreSQL
        $sql = <<<SQL
            WITH RECURSIVE comment_tree AS (
                -- Base case: root comments (no parent)
                SELECT
                    id, post_id, parent_id, user_id, author_type, author_name, author_email, author_website,
                    content, status, ip_address, user_agent, approved_at, approved_by,
                    created_at, updated_at,
                    0 as depth,
                    ARRAY[id] as path
                FROM comments
                WHERE post_id = ? AND parent_id IS NULL AND status = 'approved'

                UNION ALL

                -- Recursive case: child comments
                SELECT
                    c.id, c.post_id, c.parent_id, c.user_id, c.author_type, c.author_name, c.author_email, c.author_website,
                    c.content, c.status, c.ip_address, c.user_agent, c.approved_at, c.approved_by,
                    c.created_at, c.updated_at,
                    ct.depth + 1,
                    ct.path || c.id
                FROM comments c
                INNER JOIN comment_tree ct ON c.parent_id = ct.id
                WHERE c.status = 'approved'
            )
            SELECT * FROM comment_tree ORDER BY path
        SQL;

        // Execute the query
        $flatComments = DB::select($sql, [$postId->value()]);

        // Build nested tree structure
        return $this->buildNestedTree($flatComments);
    }

    /**
     * Build nested tree structure from flat comment results.
     * Returns array with structure: [{id, content, author, depth, replies: [...]}]
     */
    private function buildNestedTree(array $flatComments): array
    {
        if (empty($flatComments)) {
            return [];
        }

        // Group comments by parent_id for easy lookup
        $grouped = ['root' => []];
        foreach ($flatComments as $comment) {
            $parentKey = $comment->parent_id ?? 'root';
            if (!isset($grouped[$parentKey])) {
                $grouped[$parentKey] = [];
            }
            $grouped[$parentKey][] = $comment;
        }

        // Build tree recursively starting from root
        return $this->buildNode($grouped, 'root');
    }

    /**
     * Recursively build tree nodes.
     */
    private function buildNode(array $grouped, string $parentId): array
    {
        $branch = [];

        if (!isset($grouped[$parentId])) {
            return $branch;
        }

        foreach ($grouped[$parentId] as $comment) {
            // Format author data
            $author = [];
            if ($comment->author_type === 'registered') {
                $author = [
                    'type' => 'registered',
                    'user_id' => $comment->user_id,
                ];
            } else {
                $author = [
                    'type' => 'anonymous',
                    'name' => $comment->author_name,
                    'email' => $comment->author_email,
                    'website' => $comment->author_website,
                ];
            }

            $node = [
                'id' => $comment->id,
                'content' => $comment->content,
                'author' => $author,
                'depth' => $comment->depth,
                'created_at' => $comment->created_at,
                'replies' => $this->buildNode($grouped, $comment->id),
            ];

            $branch[] = $node;
        }

        return $branch;
    }

    public function countByPostId(PostId $postId): int
    {
        return CommentModel::where('post_id', $postId->value())->count();
    }

    public function countApprovedByPostId(PostId $postId): int
    {
        return CommentModel::where('post_id', $postId->value())
            ->where('status', 'approved')
            ->count();
    }
}
