<?php

declare(strict_types=1);

namespace Src\Domain\Page\ValueObjects;

final readonly class PageTranslation
{
    public function __construct(
        private string $locale,
        private PageTitle $title,
        private PageContent $content,
        private ?string $metaDescription = null
    ) {
    }

    public static function create(
        string $locale,
        string $title,
        string $content,
        ?string $metaDescription = null
    ): self {
        return new self(
            $locale,
            PageTitle::fromString($title),
            PageContent::fromString($content),
            $metaDescription
        );
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function title(): PageTitle
    {
        return $this->title;
    }

    public function content(): PageContent
    {
        return $this->content;
    }

    public function metaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function toArray(): array
    {
        return [
            'locale' => $this->locale,
            'title' => $this->title->value(),
            'content' => $this->content->value(),
            'meta_description' => $this->metaDescription,
        ];
    }
}
