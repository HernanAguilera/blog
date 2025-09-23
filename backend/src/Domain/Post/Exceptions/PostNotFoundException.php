<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostSlug;
use DomainException;

final class PostNotFoundException extends DomainException
{
    public function __construct(
        string $message = 'Post not found',
        int $code = 404
    ) {
        parent::__construct($message, $code);
    }

    public static function withId(PostId $id): self
    {
        return new self("Post with ID '{$id->value()}' not found");
    }

    public static function withSlug(PostSlug $slug): self
    {
        return new self("Post with slug '{$slug->value()}' not found");
    }

    public static function general(): self
    {
        return new self('The requested post could not be found');
    }
}