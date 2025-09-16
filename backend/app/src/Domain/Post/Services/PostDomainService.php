<?php

declare(strict_types=1);

namespace App\src\Domain\Post\Services;

use App\src\Domain\Post\Entities\Post;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Domain\Post\ValueObjects\PostSlug;
use App\src\Domain\Post\ValueObjects\PostTitle;
use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\Post\ValueObjects\PostContent;
use App\src\Domain\Post\ValueObjects\ReadingTime;

final class PostDomainService
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
    }

    /**
     * Generate a unique slug from a title.
     * If the slug already exists, append a number to make it unique.
     */
    public function generateUniqueSlug(PostTitle $title, ?PostId $excludePostId = null): PostSlug
    {
        $baseSlug = $this->generateSlugFromTitle($title->value());
        $slug = new PostSlug($baseSlug);

        // Check if slug is already unique
        if ($this->postRepository->isSlugUniqueForPost($slug, $excludePostId)) {
            return $slug;
        }

        // If not unique, try appending numbers
        $counter = 2;
        do {
            $uniqueSlugValue = $baseSlug . '-' . $counter;
            $uniqueSlug = new PostSlug($uniqueSlugValue);
            $counter++;
        } while (!$this->postRepository->isSlugUniqueForPost($uniqueSlug, $excludePostId) && $counter <= 100);

        if ($counter > 100) {
            // Fallback: append timestamp
            $timestampSlug = $baseSlug . '-' . time();
            return new PostSlug($timestampSlug);
        }

        return $uniqueSlug;
    }

    /**
     * Validate that a slug is unique for a post.
     */
    public function validateSlugUniqueness(PostSlug $slug, ?PostId $excludePostId = null): bool
    {
        return $this->postRepository->isSlugUniqueForPost($slug, $excludePostId);
    }

    /**
     * Calculate reading time for content.
     */
    public function calculateReadingTime(PostContent $content): ReadingTime
    {
        return ReadingTime::fromContent($content);
    }

    /**
     * Check if a post can be published.
     */
    public function canBePublished(Post $post): bool
    {
        // Business rules for publishing
        $minTitleLength = 5;
        $minContentWords = 10;

        if (mb_strlen($post->getTitle()->value()) < $minTitleLength) {
            return false;
        }

        if ($post->getContent()->getWordCount() < $minContentWords) {
            return false;
        }

        // Check if slug is unique
        if (!$this->validateSlugUniqueness($post->getSlug(), $post->getId())) {
            return false;
        }

        return true;
    }

    /**
     * Get publishing requirements for a post.
     *
     * @return array<string, mixed>
     */
    public function getPublishingRequirements(Post $post): array
    {
        $requirements = [
            'can_publish' => true,
            'issues' => [],
            'warnings' => []
        ];

        // Check title length
        if (mb_strlen($post->getTitle()->value()) < 5) {
            $requirements['can_publish'] = false;
            $requirements['issues'][] = 'Title must be at least 5 characters long';
        }

        // Check content length
        if ($post->getContent()->getWordCount() < 10) {
            $requirements['can_publish'] = false;
            $requirements['issues'][] = 'Content must have at least 10 words';
        }

        // Check slug uniqueness
        if (!$this->validateSlugUniqueness($post->getSlug(), $post->getId())) {
            $requirements['can_publish'] = false;
            $requirements['issues'][] = 'Slug must be unique';
        }

        // Check meta description
        if ($post->getMetaDescription() === null || $post->getMetaDescription()->isEmpty()) {
            $requirements['warnings'][] = 'Meta description is missing (will be auto-generated)';
        } elseif (!$post->getMetaDescription()->isOptimal()) {
            $requirements['warnings'][] = 'Meta description should be 50-160 characters for better SEO';
        }

        // Check reading time
        if ($post->getReadingTime()->value() < 1) {
            $requirements['warnings'][] = 'Content is very short (less than 1 minute read)';
        }

        return $requirements;
    }

    /**
     * Suggest improvements for a post.
     *
     * @return array<string>
     */
    public function suggestImprovements(Post $post): array
    {
        $suggestions = [];

        $readingTime = $post->getReadingTime();
        $wordCount = $post->getContent()->getWordCount();

        // Content length suggestions
        if ($readingTime->isShortRead()) {
            $suggestions[] = 'Consider expanding the content for better engagement (currently ' . $readingTime->value() . ' min read)';
        }

        // Meta description suggestions
        if ($post->getMetaDescription() === null || $post->getMetaDescription()->isEmpty()) {
            $suggestions[] = 'Add a custom meta description to improve SEO';
        }

        // Title suggestions
        $title = $post->getTitle()->value();
        if (mb_strlen($title) < 30) {
            $suggestions[] = 'Consider a longer, more descriptive title for better SEO';
        }

        // Content structure suggestions
        if ($wordCount > 1000 && !$this->hasSubheadings($post->getContent())) {
            $suggestions[] = 'Consider adding subheadings (H2, H3) to improve readability for long content';
        }

        return $suggestions;
    }

    /**
     * Generate slug from title with proper formatting.
     */
    private function generateSlugFromTitle(string $title): string
    {
        // Convert to lowercase
        $slug = mb_strtolower($title, 'UTF-8');

        // Replace accented characters
        $slug = $this->removeAccents($slug);

        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');

        // Limit length
        if (mb_strlen($slug) > 100) {
            $slug = mb_substr($slug, 0, 100);
            // Try to cut at word boundary
            $lastHyphen = mb_strrpos($slug, '-');
            if ($lastHyphen !== false && $lastHyphen > 50) {
                $slug = mb_substr($slug, 0, $lastHyphen);
            }
        }

        // Ensure minimum length
        if (mb_strlen($slug) < 3) {
            $slug = 'post-' . time();
        }

        return $slug;
    }

    /**
     * Remove accents from string.
     */
    private function removeAccents(string $string): string
    {
        $accents = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ā' => 'a', 'ã' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e', 'ē' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i', 'ī' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'ō' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u', 'ū' => 'u',
            'ñ' => 'n', 'ç' => 'c',
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ā' => 'A', 'Ã' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E', 'Ē' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I', 'Ī' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Ō' => 'O', 'Õ' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U', 'Ū' => 'U',
            'Ñ' => 'N', 'Ç' => 'C'
        ];

        return strtr($string, $accents);
    }

    /**
     * Check if content has subheadings.
     */
    private function hasSubheadings(PostContent $content): bool
    {
        $htmlContent = $content->value();
        return preg_match('/<h[2-6][^>]*>/i', $htmlContent) === 1;
    }

    /**
     * Validate post for specific business rules.
     */
    public function validatePost(Post $post): array
    {
        $errors = [];

        // Title validation
        if (mb_strlen($post->getTitle()->value()) < 5) {
            $errors[] = 'Title must be at least 5 characters long';
        }

        // Content validation
        if ($post->getContent()->getWordCount() < 10) {
            $errors[] = 'Content must have at least 10 words';
        }

        // Slug validation
        if (!$this->validateSlugUniqueness($post->getSlug(), $post->getId())) {
            $errors[] = 'Slug must be unique';
        }

        return $errors;
    }
}