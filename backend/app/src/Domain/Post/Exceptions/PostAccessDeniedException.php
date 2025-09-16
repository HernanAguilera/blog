<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Exceptions;

use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\User\ValueObjects\UserId;
use DomainException;

final class PostAccessDeniedException extends DomainException
{
    public function __construct(
        string $message = 'Access denied to post',
        int $code = 403
    ) {
        parent::__construct($message, $code);
    }

    public static function cannotEdit(PostId $postId, UserId $userId): self
    {
        return new self(
            "User '{$userId->value()}' cannot edit post '{$postId->value()}'"
        );
    }

    public static function cannotDelete(PostId $postId, UserId $userId): self
    {
        return new self(
            "User '{$userId->value()}' cannot delete post '{$postId->value()}'"
        );
    }

    public static function cannotPublish(PostId $postId, UserId $userId): self
    {
        return new self(
            "User '{$userId->value()}' cannot publish post '{$postId->value()}'"
        );
    }

    public static function general(): self
    {
        return new self('You do not have permission to perform this action on this post');
    }
}