<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Events;

use App\src\Domain\Events\DomainEvent;
use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\User\ValueObjects\UserId;

final class PostDeleted extends DomainEvent
{
    public function __construct(
        private readonly PostId $postId,
        private readonly string $title,
        private readonly string $slug,
        private readonly UserId $authorId,
        private readonly UserId $deletedBy
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'post.deleted';
    }

    public function getPostId(): PostId
    {
        return $this->postId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAuthorId(): UserId
    {
        return $this->authorId;
    }

    public function getDeletedBy(): UserId
    {
        return $this->deletedBy;
    }

    public function toPrimitives(): array
    {
        return [
            'post_id' => $this->postId->value(),
            'title' => $this->title,
            'slug' => $this->slug,
            'author_id' => $this->authorId->value(),
            'deleted_by' => $this->deletedBy->value(),
            'deleted_at' => $this->occurredOn()->format('Y-m-d H:i:s')
        ];
    }
}