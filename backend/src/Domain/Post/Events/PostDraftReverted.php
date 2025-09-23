<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Events;

use Blog\Domain\Events\DomainEvent;
use Blog\Domain\Post\Entities\Post;

final class PostDraftReverted extends DomainEvent
{
    public function __construct(
        private readonly Post $post,
        private readonly string $previousStatus
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'post.draft_reverted';
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getPreviousStatus(): string
    {
        return $this->previousStatus;
    }

    public function toPrimitives(): array
    {
        return [
            'post_id' => $this->post->getId()?->value(),
            'title' => $this->post->getTitle()->value(),
            'slug' => $this->post->getSlug()->value(),
            'author_id' => $this->post->getAuthorId()->value(),
            'previous_status' => $this->previousStatus,
            'current_status' => $this->post->getStatus()->value(),
            'reverted_at' => $this->occurredOn()->format('Y-m-d H:i:s')
        ];
    }
}