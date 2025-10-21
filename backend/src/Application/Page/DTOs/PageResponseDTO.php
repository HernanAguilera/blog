<?php

declare(strict_types=1);

namespace Src\Application\Page\DTOs;

use Src\Domain\Page\Page;

final readonly class PageResponseDTO
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $status,
        public array $translations,
        public string $createdAt,
        public string $updatedAt
    ) {
    }

    public static function fromDomain(Page $page): self
    {
        $translations = [];
        foreach ($page->translations() as $translation) {
            $translations[] = $translation->toArray();
        }

        return new self(
            id: $page->id()->value(),
            slug: $page->slug()->value(),
            status: $page->status()->value(),
            translations: $translations,
            createdAt: $page->createdAt()->format('Y-m-d H:i:s'),
            updatedAt: $page->updatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'status' => $this->status,
            'translations' => $this->translations,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
