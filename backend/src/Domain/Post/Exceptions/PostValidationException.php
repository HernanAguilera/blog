<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Exceptions;

use DomainException;

final class PostValidationException extends DomainException
{
    private array $validationErrors = [];

    public function __construct(
        string $message = 'Post validation failed',
        array $validationErrors = [],
        int $code = 422
    ) {
        parent::__construct($message, $code);
        $this->validationErrors = $validationErrors;
    }

    public static function withErrors(array $errors): self
    {
        $message = 'Post validation failed: ' . implode(', ', $errors);
        return new self($message, $errors);
    }

    public static function titleTooShort(int $minLength): self
    {
        return new self("Post title must be at least {$minLength} characters long");
    }

    public static function titleTooLong(int $maxLength): self
    {
        return new self("Post title cannot exceed {$maxLength} characters");
    }

    public static function contentTooShort(int $minWords): self
    {
        return new self("Post content must have at least {$minWords} words");
    }

    public static function contentTooLong(int $maxLength): self
    {
        return new self("Post content cannot exceed {$maxLength} characters");
    }

    public static function invalidSlugFormat(): self
    {
        return new self('Post slug must contain only lowercase letters, numbers and hyphens');
    }

    public static function slugTooShort(int $minLength): self
    {
        return new self("Post slug must be at least {$minLength} characters long");
    }

    public static function slugTooLong(int $maxLength): self
    {
        return new self("Post slug cannot exceed {$maxLength} characters");
    }

    public static function metaDescriptionTooLong(int $maxLength): self
    {
        return new self("Meta description cannot exceed {$maxLength} characters");
    }

    public static function cannotPublishIncompletePost(): self
    {
        return new self('Cannot publish post: Post does not meet minimum requirements for publishing');
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function hasValidationErrors(): bool
    {
        return !empty($this->validationErrors);
    }
}