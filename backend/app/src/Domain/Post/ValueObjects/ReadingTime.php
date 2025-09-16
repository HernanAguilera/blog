<?php

declare(strict_types=1);

namespace App\src\Domain\Post\ValueObjects;

use InvalidArgumentException;

final readonly class ReadingTime
{
    private const WORDS_PER_MINUTE = 200; // Average reading speed
    private const MIN_READING_TIME = 1; // Minimum 1 minute

    public function __construct(
        private int $minutes
    ) {
        if ($minutes < 0) {
            throw new InvalidArgumentException('Reading time cannot be negative');
        }
    }

    public function value(): int
    {
        return $this->minutes;
    }

    public function equals(ReadingTime $other): bool
    {
        return $this->minutes === $other->minutes;
    }

    public function __toString(): string
    {
        return $this->minutes . ' min';
    }

    public static function fromWordCount(int $wordCount): self
    {
        if ($wordCount <= 0) {
            return new self(self::MIN_READING_TIME);
        }

        $minutes = max(
            self::MIN_READING_TIME,
            (int) ceil($wordCount / self::WORDS_PER_MINUTE)
        );

        return new self($minutes);
    }

    public static function fromContent(PostContent $content): self
    {
        return self::fromWordCount($content->getWordCount());
    }

    public function isShortRead(): bool
    {
        return $this->minutes <= 3;
    }

    public function isMediumRead(): bool
    {
        return $this->minutes > 3 && $this->minutes <= 10;
    }

    public function isLongRead(): bool
    {
        return $this->minutes > 10;
    }
}