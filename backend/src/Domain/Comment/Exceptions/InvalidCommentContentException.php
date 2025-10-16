<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Exceptions;

use DomainException;

final class InvalidCommentContentException extends DomainException
{
    public function __construct(
        string $message = 'Invalid comment content',
        int $code = 400
    ) {
        parent::__construct($message, $code);
    }

    public static function tooShort(int $minLength, int $actualLength): self
    {
        return new self(
            "Comment content must be at least {$minLength} characters long. Got {$actualLength} characters."
        );
    }

    public static function tooLong(int $maxLength, int $actualLength): self
    {
        return new self(
            "Comment content cannot exceed {$maxLength} characters. Got {$actualLength} characters."
        );
    }

    public static function empty(): self
    {
        return new self('Comment content cannot be empty or only whitespace');
    }

    public static function containsSpam(): self
    {
        return new self('Comment content appears to contain spam');
    }

    public static function lowQuality(): self
    {
        return new self('Comment content does not meet minimum quality standards');
    }

    public static function repetitive(): self
    {
        return new self('Comment content appears to be repetitive or invalid');
    }
}
