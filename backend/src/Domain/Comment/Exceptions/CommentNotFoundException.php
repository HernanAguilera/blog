<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Exceptions;

use Blog\Domain\Comment\ValueObjects\CommentId;
use DomainException;

final class CommentNotFoundException extends DomainException
{
    public function __construct(
        string $message = 'Comment not found',
        int $code = 404
    ) {
        parent::__construct($message, $code);
    }

    public static function withId(CommentId $id): self
    {
        return new self("Comment with ID '{$id->value()}' not found");
    }

    public static function general(): self
    {
        return new self('The requested comment could not be found');
    }
}
