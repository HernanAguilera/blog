<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Services;

use Blog\Domain\Comment\ValueObjects\CommentContent;
use Blog\Domain\Comment\ValueObjects\CommentAuthorType;
use Blog\Domain\User\ValueObjects\UserId;

final class CommentDomainService
{
    /**
     * Sanitize comment content removing dangerous HTML.
     * Allows only safe tags: p, br, strong, em, a, code
     */
    public function sanitizeContent(string $content): string
    {
        // Strip all HTML tags except safe ones
        $allowedTags = '<p><br><strong><em><a><code>';
        $sanitized = strip_tags($content, $allowedTags);

        // Additional sanitization for links
        $sanitized = $this->sanitizeLinks($sanitized);

        return trim($sanitized);
    }

    /**
     * Determine if comment should be auto-approved based on author type.
     * - Registered users: auto-approve
     * - Anonymous users: pending approval
     */
    public function shouldAutoApprove(CommentAuthorType $authorType, ?UserId $userId): bool
    {
        // Only registered users with valid userId get auto-approved
        if ($authorType->isRegistered() && $userId !== null) {
            return true;
        }

        // All anonymous comments need approval
        return false;
    }

    /**
     * Basic spam detection for comment content.
     * Returns true if content appears to be spam.
     */
    public function isSpamContent(string $content): bool
    {
        $content = mb_strtolower($content);

        // Check for excessive URLs
        $urlCount = preg_match_all('/https?:\/\//', $content);
        if ($urlCount > 3) {
            return true;
        }

        // Check for common spam keywords
        $spamKeywords = [
            'buy now',
            'click here',
            'earn money',
            'free money',
            'viagra',
            'cialis',
            'casino',
            'poker online',
            'weight loss',
            'bitcoin',
            'cryptocurrency',
            'work from home',
            'make money fast',
        ];

        foreach ($spamKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }

        // Check for excessive capitalization (more than 50% uppercase)
        $upperCount = preg_match_all('/[A-Z]/', $content);
        $letterCount = preg_match_all('/[a-zA-Z]/', $content);
        if ($letterCount > 0 && ($upperCount / $letterCount) > 0.5) {
            return true;
        }

        // Check for excessive special characters
        $specialCharCount = preg_match_all('/[!@#$%^&*()_+=\[\]{};:\'",.<>?\/\\|`~]/', $content);
        if ($specialCharCount > (mb_strlen($content) * 0.3)) {
            return true;
        }

        return false;
    }

    /**
     * Validate if a user can post a comment.
     * Basic rate limiting check (actual implementation will be in Application layer).
     */
    public function canUserComment(UserId $userId): bool
    {
        // This is a placeholder for domain-level validation
        // Actual rate limiting will be implemented in Application layer with middleware
        return true;
    }

    /**
     * Sanitize links in content.
     * Ensures all links have safe attributes and no javascript: protocol.
     */
    private function sanitizeLinks(string $content): string
    {
        // Remove javascript: protocol from links
        $content = preg_replace('/(<a[^>]+href\s*=\s*["\'])javascript:/i', '$1#', $content);

        // Remove on* event handlers
        $content = preg_replace('/<a[^>]+\Kon\w+\s*=\s*["\'][^"\']*["\']/i', '', $content);

        // Add nofollow and noopener to all external links
        $content = preg_replace_callback(
            '/<a([^>]+)>/i',
            function ($matches) {
                $attributes = $matches[1];

                // Add rel="nofollow noopener" if not present
                if (!str_contains($attributes, 'rel=')) {
                    $attributes .= ' rel="nofollow noopener"';
                }

                // Add target="_blank" for external links if not present
                if (!str_contains($attributes, 'target=')) {
                    $attributes .= ' target="_blank"';
                }

                return '<a' . $attributes . '>';
            },
            $content
        );

        return $content;
    }

    /**
     * Validate comment content meets minimum quality standards.
     */
    public function validateContentQuality(CommentContent $content): array
    {
        $errors = [];
        $value = $content->value();

        // Check if content is not just whitespace or special characters
        $alphanumericCount = preg_match_all('/[a-zA-Z0-9]/', $value);
        if ($alphanumericCount < 3) {
            $errors[] = 'Comment must contain at least 3 alphanumeric characters';
        }

        // Check if content is not repetitive (e.g., "aaaaaaa")
        if ($this->isRepetitive($value)) {
            $errors[] = 'Comment appears to be repetitive or invalid';
        }

        return $errors;
    }

    /**
     * Check if content is repetitive.
     */
    private function isRepetitive(string $content): bool
    {
        // Check for repeated characters (more than 5 in a row)
        if (preg_match('/(.)\1{5,}/', $content)) {
            return true;
        }

        // Check for repeated words (more than 3 times)
        $words = str_word_count($content, 1);
        if (count($words) > 0) {
            $wordCounts = array_count_values($words);
            $maxRepetition = max($wordCounts);
            if ($maxRepetition > 3 && count($words) < 20) {
                return true;
            }
        }

        return false;
    }
}
