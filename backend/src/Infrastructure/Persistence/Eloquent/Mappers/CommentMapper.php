<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Persistence\Eloquent\Mappers;

use Blog\Domain\Comment\Entities\Comment;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\ValueObjects\CommentContent;
use Blog\Domain\Comment\ValueObjects\CommentStatus;
use Blog\Domain\Comment\ValueObjects\CommentAuthorType;
use Blog\Domain\Comment\ValueObjects\AnonymousAuthor;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Infrastructure\Persistence\Eloquent\Models\CommentModel;

final class CommentMapper
{
    public static function toDomain(CommentModel $model): Comment
    {
        $authorType = new CommentAuthorType($model->author_type);

        $userId = $model->user_id ? new UserId($model->user_id) : null;

        $anonymousAuthor = null;
        if ($authorType->isAnonymous()) {
            $anonymousAuthor = new AnonymousAuthor(
                $model->author_name,
                $model->author_email,
                $model->author_website
            );
        }

        return new Comment(
            id: $model->id ? CommentId::fromString($model->id) : null,
            postId: new PostId($model->post_id),
            authorType: $authorType,
            content: new CommentContent($model->content),
            status: new CommentStatus($model->status),
            userId: $userId,
            anonymousAuthor: $anonymousAuthor,
            parentId: $model->parent_id ? CommentId::fromString($model->parent_id) : null,
            ipAddress: $model->ip_address,
            userAgent: $model->user_agent,
            createdAt: $model->created_at
        );
    }

    public static function toEloquent(Comment $comment): CommentModel
    {
        $model = new CommentModel();

        if ($comment->id() !== null) {
            $model->id = $comment->id()->value();
            $model->exists = true;
        }

        $model->post_id = $comment->postId()->value();
        $model->user_id = $comment->userId()?->value();
        $model->author_type = $comment->authorType()->value();

        // Anonymous author data
        if ($comment->anonymousAuthor()) {
            $model->author_name = $comment->anonymousAuthor()->name();
            $model->author_email = $comment->anonymousAuthor()->email();
            $model->author_website = $comment->anonymousAuthor()->website();
        }

        $model->content = $comment->content()->value();
        $model->status = $comment->status()->value();
        $model->parent_id = $comment->parentId()?->value();
        $model->ip_address = $comment->ipAddress();
        $model->user_agent = $comment->userAgent();
        $model->approved_at = $comment->approvedAt();
        $model->approved_by = $comment->approvedBy()?->value();

        return $model;
    }

    public static function updateEloquentFromDomain(CommentModel $model, Comment $comment): CommentModel
    {
        $model->content = $comment->content()->value();
        $model->status = $comment->status()->value();
        $model->approved_at = $comment->approvedAt();
        $model->approved_by = $comment->approvedBy()?->value();

        return $model;
    }

    /**
     * @param CommentModel[]|\Illuminate\Database\Eloquent\Collection $models
     * @return Comment[]
     */
    public static function toDomainCollection($models): array
    {
        if ($models instanceof \Illuminate\Database\Eloquent\Collection) {
            return $models->map(fn(CommentModel $model) => self::toDomain($model))->toArray();
        }

        return array_map(
            fn(CommentModel $model) => self::toDomain($model),
            $models
        );
    }
}
