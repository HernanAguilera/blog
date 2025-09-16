<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Events;

use App\src\Domain\Shared\Events\DomainEvent;
use DateTimeImmutable;

final readonly class PostScheduled implements DomainEvent
{
    public function __construct(
        private int $postId,
        private string $title,
        private string $slug,
        private int $authorId,
        private DateTimeImmutable $scheduledAt,
        private DateTimeImmutable $occurredOn = new DateTimeImmutable()
    ) {
    }

    public function postId(): int
    {
        return $this->postId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function authorId(): int
    {
        return $this->authorId;
    }

    public function scheduledAt(): DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function eventName(): string
    {
        return 'post.scheduled';
    }

    public function toPrimitives(): array
    {
        return [
            'post_id' => $this->postId,
            'title' => $this->title,
            'slug' => $this->slug,
            'author_id' => $this->authorId,
            'scheduled_at' => $this->scheduledAt->format('Y-m-d H:i:s'),
            'occurred_on' => $this->occurredOn->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            postId: $data['post_id'],
            title: $data['title'],
            slug: $data['slug'],
            authorId: $data['author_id'],
            scheduledAt: new DateTimeImmutable($data['scheduled_at']),
            occurredOn: new DateTimeImmutable($data['occurred_on'])
        );
    }
}