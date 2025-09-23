<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Services;

use Blog\Domain\Post\ValueObjects\HtmlContent;

interface HtmlSanitizerInterface
{
    public function sanitize(string $html): HtmlContent;

    public function sanitizeForPreview(string $html): HtmlContent;

    public function getAllowedTags(): array;

    public function isContentSafe(string $html): bool;
}