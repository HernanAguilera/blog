<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Entities;

use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\Post\ValueObjects\PostTitle;
use App\src\Domain\Post\ValueObjects\PostSlug;
use App\src\Domain\Post\ValueObjects\PostContent;
use App\src\Domain\Post\ValueObjects\PostStatus;
use App\src\Domain\Post\ValueObjects\ReadingTime;
use App\src\Domain\Post\ValueObjects\MetaDescription;
use App\src\Domain\Post\Services\PostStateValidator;
use App\src\Domain\User\ValueObjects\UserId;
use DateTimeImmutable;

final class Post
{
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $publishedAt = null;
    private ?DateTimeImmutable $scheduledAt = null;
    private ReadingTime $readingTime;

    public function __construct(
        private ?PostId $id,
        private PostTitle $title,
        private PostSlug $slug,
        private PostContent $content,
        private PostStatus $status,
        private UserId $authorId,
        private ?MetaDescription $metaDescription = null,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->readingTime = ReadingTime::fromContent($content);

        // Don't auto-generate meta description - let it be null if not provided
        // if ($this->metaDescription === null) {
        //     $this->metaDescription = MetaDescription::fromContent($content);
        // }
    }

    public static function create(
        PostTitle $title,
        PostContent $content,
        UserId $authorId,
        ?PostSlug $slug = null,
        ?MetaDescription $metaDescription = null
    ): self {
        $generatedSlug = $slug ?? PostSlug::fromTitle($title->value());

        return new self(
            id: null,
            title: $title,
            slug: $generatedSlug,
            content: $content,
            status: PostStatus::draft(),
            authorId: $authorId,
            metaDescription: $metaDescription
        );
    }

    public static function fromPrimitives(
        ?int $id,
        string $title,
        string $slug,
        string $content,
        string $status,
        int $authorId,
        ?string $metaDescription = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?string $publishedAt = null,
        ?string $scheduledAt = null
    ): self {
        $post = new self(
            id: $id ? new PostId($id) : null,
            title: new PostTitle($title),
            slug: new PostSlug($slug),
            content: new PostContent($content),
            status: new PostStatus($status),
            authorId: new UserId($authorId),
            metaDescription: $metaDescription ? new MetaDescription($metaDescription) : null,
            createdAt: $createdAt ? new DateTimeImmutable($createdAt) : new DateTimeImmutable()
        );

        if ($updatedAt) {
            $post->updatedAt = new DateTimeImmutable($updatedAt);
        }

        if ($publishedAt) {
            $post->publishedAt = new DateTimeImmutable($publishedAt);
        }

        if ($scheduledAt) {
            $post->scheduledAt = new DateTimeImmutable($scheduledAt);
        }

        return $post;
    }

    // Getters
    public function getId(): ?PostId
    {
        return $this->id;
    }

    public function setId(PostId $id): void
    {
        if ($this->id !== null) {
            throw new \LogicException('Post ID cannot be changed once set');
        }
        $this->id = $id;
    }

    public function getTitle(): PostTitle
    {
        return $this->title;
    }

    public function getSlug(): PostSlug
    {
        return $this->slug;
    }

    public function getContent(): PostContent
    {
        return $this->content;
    }

    public function getStatus(): PostStatus
    {
        return $this->status;
    }

    public function getAuthorId(): UserId
    {
        return $this->authorId;
    }

    public function getMetaDescription(): ?MetaDescription
    {
        return $this->metaDescription;
    }

    public function getReadingTime(): ReadingTime
    {
        return $this->readingTime;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getScheduledAt(): ?DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    // Business methods
    public function updateTitle(PostTitle $title): void
    {
        $this->title = $title;
        $this->markAsUpdated();
    }

    public function updateSlug(PostSlug $slug): void
    {
        $this->slug = $slug;
        $this->markAsUpdated();
    }

    public function updateContent(PostContent $content): void
    {
        $this->content = $content;
        $this->readingTime = ReadingTime::fromContent($content);

        // Auto-update meta description if it was auto-generated
        if ($this->metaDescription === null || !$this->metaDescription->isOptimal()) {
            $this->metaDescription = MetaDescription::fromContent($content);
        }

        $this->markAsUpdated();
    }

    public function updateMetaDescription(?MetaDescription $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
        $this->markAsUpdated();
    }

    public function publish(): void
    {
        if ($this->status->isPublished()) {
            return; // Already published
        }

        $validator = new PostStateValidator();
        $validator->validateTransition($this, PostStatus::published());

        $this->status = PostStatus::published();
        $this->publishedAt = new DateTimeImmutable();
        $this->scheduledAt = null; // Clear scheduled date when publishing
        $this->markAsUpdated();
    }

    public function archive(): void
    {
        if ($this->status->isArchived()) {
            return; // Already archived
        }

        $validator = new PostStateValidator();
        $validator->validateTransition($this, PostStatus::archived());

        $this->status = PostStatus::archived();
        $this->markAsUpdated();
    }

    public function makeDraft(): void
    {
        if ($this->status->isDraft()) {
            return; // Already draft
        }

        $validator = new PostStateValidator();
        $validator->validateTransition($this, PostStatus::draft());

        $this->status = PostStatus::draft();
        $this->publishedAt = null; // Clear published date when reverting to draft
        $this->scheduledAt = null; // Clear scheduled date when reverting to draft
        $this->markAsUpdated();
    }

    public function schedule(DateTimeImmutable $scheduledAt): void
    {
        $validator = new PostStateValidator();

        // Validate the scheduling constraints
        $validator->validateScheduling($this, $scheduledAt);

        // Validate the status transition
        $validator->validateTransition($this, PostStatus::scheduled());

        $this->status = PostStatus::scheduled();
        $this->scheduledAt = $scheduledAt;
        $this->publishedAt = null; // Clear published date when scheduling
        $this->markAsUpdated();
    }

    // Status checks
    public function isDraft(): bool
    {
        return $this->status->isDraft();
    }

    public function isPublished(): bool
    {
        return $this->status->isPublished();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isScheduled(): bool
    {
        return $this->status->isScheduled();
    }

    public function isPublic(): bool
    {
        return $this->isPublished() && $this->publishedAt !== null;
    }

    public function isReadyToPublish(): bool
    {
        return $this->isScheduled()
            && $this->scheduledAt !== null
            && $this->scheduledAt <= new DateTimeImmutable();
    }

    public function getAvailableTransitions(): array
    {
        $validator = new PostStateValidator();
        return $validator->getAvailableTransitions($this);
    }

    // Utility methods
    public function canBeEditedBy(UserId $userId): bool
    {
        return $this->authorId->equals($userId);
    }

    public function getWordCount(): int
    {
        return $this->content->getWordCount();
    }

    public function getPlainTextContent(): string
    {
        return $this->content->getPlainText();
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id?->value(),
            'title' => $this->title->value(),
            'slug' => $this->slug->value(),
            'content' => $this->content->value(),
            'status' => $this->status->value(),
            'author_id' => $this->authorId->value(),
            'meta_description' => $this->metaDescription?->value(),
            'reading_time' => $this->readingTime->value(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'published_at' => $this->publishedAt?->format('Y-m-d H:i:s'),
            'scheduled_at' => $this->scheduledAt?->format('Y-m-d H:i:s'),
        ];
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}