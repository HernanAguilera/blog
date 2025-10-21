<?php

declare(strict_types=1);

namespace Src\Application\Page\DTOs;

final readonly class CreatePageDTO
{
    /**
     * @param string $slug
     * @param string $status
     * @param array<string, array{title: string, content: string, meta_description: ?string}> $translations
     */
    public function __construct(
        public string $slug,
        public string $status,
        public array $translations
    ) {
    }
}
