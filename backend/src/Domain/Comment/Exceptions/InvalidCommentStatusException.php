<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Exceptions;

use Blog\Domain\Comment\ValueObjects\CommentStatus;
use DomainException;

final class InvalidCommentStatusException extends DomainException
{
    public function __construct(
        string $message = 'Invalid comment status',
        int $code = 400
    ) {
        parent::__construct($message, $code);
    }

    public static function forTransition(CommentStatus $from, CommentStatus $to): self
    {
        return new self(
            "Cannot transition comment status from '{$from->value()}' to '{$to->value()}'"
        );
    }

    public static function cannotApprove(CommentStatus $current): self
    {
        return new self(
            "Cannot approve comment with status '{$current->value()}'"
        );
    }

    public static function cannotReject(CommentStatus $current): self
    {
        return new self(
            "Cannot reject comment with status '{$current->value()}'"
        );
    }

    public static function alreadyApproved(): self
    {
        return new self('Comment is already approved');
    }

    public static function alreadyRejected(): self
    {
        return new self('Comment is already rejected');
    }

    public static function alreadySpam(): self
    {
        return new self('Comment is already marked as spam');
    }
}
