<?php

declare(strict_types=1);

namespace Blog\Application\Services\Moderation;

use Blog\Domain\Comment\Entities\Comment;
use Blog\Domain\Comment\Services\CommentDomainService;
use Blog\Domain\Comment\ValueObjects\CommentContent;

final readonly class ModerationService
{
    public function __construct(
        private CommentDomainService $domainService,
        private array $config = []
    ) {}

    /**
     * Determine if a comment should be auto-approved.
     */
    public function shouldAutoApprove(Comment $comment): bool
    {
        // Registered users are always auto-approved
        if ($comment->authorType()->isRegistered()) {
            return $this->config['auto_approve_registered'] ?? true;
        }

        // Anonymous users are NOT auto-approved by default
        return $this->config['auto_approve_anonymous'] ?? false;
    }

    /**
     * Get moderation rules configuration.
     *
     * @return array<string, mixed>
     */
    public function getModerationRules(): array
    {
        return [
            'auto_approve_registered' => $this->config['auto_approve_registered'] ?? true,
            'auto_approve_anonymous' => $this->config['auto_approve_anonymous'] ?? false,
            'spam_detection_enabled' => $this->config['spam_detection_enabled'] ?? true,
            'profanity_filter_enabled' => $this->config['profanity_filter_enabled'] ?? true,
            'max_links_allowed' => $this->config['max_links_allowed'] ?? 3,
            'min_content_length' => 3,
            'max_content_length' => 5000,
            'notifications_enabled' => $this->config['notifications_enabled'] ?? true,
        ];
    }

    /**
     * Validate if content meets moderation rules.
     *
     * @return array<string> Array of validation errors (empty if valid)
     */
    public function validateModerationRules(string $content): array
    {
        $errors = [];

        // Check if spam detection is enabled
        if ($this->getModerationRules()['spam_detection_enabled']) {
            if ($this->domainService->isSpamContent($content)) {
                $errors[] = 'Content detected as spam';
            }
        }

        // Validate content quality
        try {
            $commentContent = new CommentContent($content);
            $qualityErrors = $this->domainService->validateContentQuality($commentContent);
            $errors = array_merge($errors, $qualityErrors);
        } catch (\InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     * Check if notifications should be sent for this comment.
     */
    public function shouldNotify(Comment $comment): bool
    {
        $rules = $this->getModerationRules();

        // Only notify if notifications are enabled
        if (!$rules['notifications_enabled']) {
            return false;
        }

        // Only notify for pending comments
        return $comment->status()->isPending();
    }

    /**
     * Get configured notification emails.
     *
     * @return array<string>
     */
    public function getNotificationEmails(): array
    {
        $emails = $this->config['notify_emails'] ?? '';

        if (empty($emails)) {
            return [];
        }

        // Split by comma and trim whitespace
        return array_map('trim', explode(',', $emails));
    }
}
