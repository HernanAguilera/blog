<?php

declare(strict_types=1);

namespace Blog\Domain\Post\Services;

use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\ValueObjects\PostStatus;
use Blog\Domain\Post\Exceptions\InvalidPostStatusException;
use DateTimeImmutable;

final class PostStateValidator
{
    /**
     * Validate if a post can transition to the given status.
     */
    public function canTransitionTo(Post $post, PostStatus $newStatus): bool
    {
        $currentStatus = $post->getStatus();

        // If same status, always allow (no-op)
        if ($currentStatus->equals($newStatus)) {
            return true;
        }

        return match ($newStatus->value()) {
            PostStatus::DRAFT => $this->canTransitionToDraft($currentStatus),
            PostStatus::SCHEDULED => $this->canTransitionToScheduled($currentStatus),
            PostStatus::PUBLISHED => $this->canTransitionToPublished($currentStatus),
            PostStatus::ARCHIVED => $this->canTransitionToArchived($currentStatus),
            default => false,
        };
    }

    /**
     * Validate and throw exception if transition is not allowed.
     */
    public function validateTransition(Post $post, PostStatus $newStatus): void
    {
        if (!$this->canTransitionTo($post, $newStatus)) {
            throw new InvalidPostStatusException(
                "Cannot transition post from '{$post->getStatus()->value()}' to '{$newStatus->value()}'"
            );
        }
    }

    /**
     * Validate scheduling constraints.
     */
    public function validateScheduling(Post $post, DateTimeImmutable $scheduledAt): void
    {
        // Cannot schedule already published posts
        if ($post->getStatus()->isPublished()) {
            throw new InvalidPostStatusException('Cannot schedule an already published post');
        }

        // Cannot schedule for past dates
        if ($scheduledAt <= new DateTimeImmutable()) {
            throw new InvalidPostStatusException('Cannot schedule a post for a past date');
        }

        // Cannot schedule more than 1 year in the future
        $maxFutureDate = new DateTimeImmutable('+1 year');
        if ($scheduledAt > $maxFutureDate) {
            throw new InvalidPostStatusException('Cannot schedule a post more than 1 year in the future');
        }
    }

    /**
     * Get available transitions for a post's current status.
     */
    public function getAvailableTransitions(Post $post): array
    {
        $currentStatus = $post->getStatus();
        $available = [];

        // Check each possible status
        foreach ([PostStatus::DRAFT, PostStatus::SCHEDULED, PostStatus::PUBLISHED, PostStatus::ARCHIVED] as $status) {
            $statusObj = new PostStatus($status);
            if ($this->canTransitionTo($post, $statusObj) && !$currentStatus->equals($statusObj)) {
                $available[] = $status;
            }
        }

        return $available;
    }

    private function canTransitionToDraft(PostStatus $currentStatus): bool
    {
        // Can always revert to draft from any status
        return true;
    }

    private function canTransitionToScheduled(PostStatus $currentStatus): bool
    {
        // Can schedule from draft or update existing scheduled post
        return $currentStatus->isDraft() || $currentStatus->isScheduled();
    }

    private function canTransitionToPublished(PostStatus $currentStatus): bool
    {
        // Can publish from draft, scheduled, or archived
        return $currentStatus->isDraft()
            || $currentStatus->isScheduled()
            || $currentStatus->isArchived();
    }

    private function canTransitionToArchived(PostStatus $currentStatus): bool
    {
        // Can archive from any status except already archived
        return !$currentStatus->isArchived();
    }
}