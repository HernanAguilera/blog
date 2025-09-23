<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use Blog\Domain\Post\ValueObjects\PostStatus;
use DomainException;

final class InvalidPostStatusException extends DomainException
{
    public function __construct(
        string $message = 'Invalid post status operation',
        int $code = 400
    ) {
        parent::__construct($message, $code);
    }

    public static function cannotTransitionFromTo(PostStatus $from, PostStatus $to): self
    {
        return new self(
            "Cannot transition post status from '{$from->value()}' to '{$to->value()}'"
        );
    }

    public static function cannotPublishDraft(): self
    {
        return new self('Cannot publish post: Post does not meet publishing requirements');
    }

    public static function cannotArchiveUnpublished(): self
    {
        return new self('Cannot archive post: Only published posts can be archived');
    }

    public static function alreadyInStatus(PostStatus $status): self
    {
        return new self("Post is already in '{$status->value()}' status");
    }

    public static function invalidStatusValue(string $status): self
    {
        return new self("Invalid post status value: '{$status}'");
    }
}