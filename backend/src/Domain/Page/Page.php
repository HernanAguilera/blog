<?php

declare(strict_types=1);

namespace Src\Domain\Page;

use DateTimeImmutable;
use Src\Domain\Page\ValueObjects\PageContent;
use Src\Domain\Page\ValueObjects\PageId;
use Src\Domain\Page\ValueObjects\PageSlug;
use Src\Domain\Page\ValueObjects\PageStatus;
use Src\Domain\Page\ValueObjects\PageTitle;
use Src\Domain\Page\ValueObjects\PageTranslation;

final class Page
{
    /**
     * @param array<PageTranslation> $translations
     */
    public function __construct(
        private PageId $id,
        private PageSlug $slug,
        private PageStatus $status,
        private array $translations,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function create(
        PageSlug $slug,
        PageStatus $status,
        array $translations
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            PageId::generate(),
            $slug,
            $status,
            $translations,
            $now,
            $now
        );
    }

    public function publish(): void
    {
        $this->status = PageStatus::PUBLISHED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function unpublish(): void
    {
        $this->status = PageStatus::DRAFT;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateTranslation(
        string $locale,
        string $title,
        string $content,
        ?string $metaDescription = null
    ): void {
        $translation = PageTranslation::create($locale, $title, $content, $metaDescription);

        // Remove existing translation for this locale if exists
        $this->translations = array_filter(
            $this->translations,
            fn(PageTranslation $t) => $t->locale() !== $locale
        );

        // Add new translation
        $this->translations[] = $translation;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getTranslation(string $locale): ?PageTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->locale() === $locale) {
                return $translation;
            }
        }

        return null;
    }

    public function hasTranslation(string $locale): bool
    {
        return $this->getTranslation($locale) !== null;
    }

    public function id(): PageId
    {
        return $this->id;
    }

    public function slug(): PageSlug
    {
        return $this->slug;
    }

    public function status(): PageStatus
    {
        return $this->status;
    }

    /**
     * @return array<PageTranslation>
     */
    public function translations(): array
    {
        return $this->translations;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isPublished(): bool
    {
        return $this->status->isPublished();
    }

    public function isDraft(): bool
    {
        return $this->status->isDraft();
    }
}
