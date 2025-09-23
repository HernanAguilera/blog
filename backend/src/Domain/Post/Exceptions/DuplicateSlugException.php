<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use Blog\Domain\Post\ValueObjects\PostSlug;
use Blog\Domain\Post\ValueObjects\PostId;
use DomainException;

final class DuplicateSlugException extends DomainException
{
    public function __construct(
        string $message = 'Post slug already exists',
        int $code = 409
    ) {
        parent::__construct($message, $code);
    }

    public static function withSlug(PostSlug $slug): self
    {
        return new self("A post with slug '{$slug->value()}' already exists");
    }

    public static function withSlugForDifferentPost(PostSlug $slug, PostId $existingPostId): self
    {
        return new self(
            "Slug '{$slug->value()}' is already used by post with ID '{$existingPostId->value()}'"
        );
    }

    public static function general(): self
    {
        return new self('The post slug must be unique');
    }
}