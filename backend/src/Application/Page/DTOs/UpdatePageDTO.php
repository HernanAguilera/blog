<?php

declare(strict_types=1);

namespace Src\Application\Page\DTOs;

final readonly class UpdatePageDTO
{
    /**
     * @param string $id
     * @param string|null $slug
     * @param string|null $status
     * @param array<string, array{title: string, content: string, meta_description: ?string}>|null $translations
     */
    public function __construct(
        public string $id,
        public ?string $slug = null,
        public ?string $status = null,
        public ?array $translations = null
    ) {
    }
}
