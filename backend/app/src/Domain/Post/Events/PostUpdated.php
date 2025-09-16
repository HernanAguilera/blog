<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Events;

use App\src\Domain\Events\DomainEvent;
use App\src\Domain\Post\Entities\Post;

final class PostUpdated extends DomainEvent
{
    public function __construct(
        private readonly Post $post,
        private readonly array $changedFields = []
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'post.updated';
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getChangedFields(): array
    {
        return $this->changedFields;
    }

    public function toPrimitives(): array
    {
        return [
            'post_id' => $this->post->getId()?->value(),
            'title' => $this->post->getTitle()->value(),
            'slug' => $this->post->getSlug()->value(),
            'status' => $this->post->getStatus()->value(),
            'author_id' => $this->post->getAuthorId()->value(),
            'reading_time' => $this->post->getReadingTime()->value(),
            'word_count' => $this->post->getWordCount(),
            'updated_at' => $this->post->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'changed_fields' => $this->changedFields
        ];
    }
}