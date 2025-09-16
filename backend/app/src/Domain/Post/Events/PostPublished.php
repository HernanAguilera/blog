<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Events;

use App\src\Domain\Events\DomainEvent;
use App\src\Domain\Post\Entities\Post;

final class PostPublished extends DomainEvent
{
    public function __construct(
        private readonly Post $post
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'post.published';
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function toPrimitives(): array
    {
        return [
            'post_id' => $this->post->getId()?->value(),
            'title' => $this->post->getTitle()->value(),
            'slug' => $this->post->getSlug()->value(),
            'author_id' => $this->post->getAuthorId()->value(),
            'reading_time' => $this->post->getReadingTime()->value(),
            'word_count' => $this->post->getWordCount(),
            'published_at' => $this->post->getPublishedAt()?->format('Y-m-d H:i:s'),
            'meta_description' => $this->post->getMetaDescription()?->value()
        ];
    }
}