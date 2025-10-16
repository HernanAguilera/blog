<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Exceptions;

use DomainException;

final class SpamCommentException extends DomainException
{
    public function __construct(
        string $message = 'Comment detected as spam',
        int $code = 403
    ) {
        parent::__construct($message, $code);
    }

    public static function detectedByContent(): self
    {
        return new self('Comment content appears to be spam and was automatically rejected');
    }

    public static function detectedByKeywords(): self
    {
        return new self('Comment contains spam keywords and was automatically rejected');
    }

    public static function excessiveUrls(): self
    {
        return new self('Comment contains too many URLs and was rejected as spam');
    }

    public static function suspiciousPattern(): self
    {
        return new self('Comment matches suspicious spam patterns and was rejected');
    }

    public static function rateLimitExceeded(): self
    {
        return new self('Too many comments in short time period. Please wait before commenting again.');
    }
}
