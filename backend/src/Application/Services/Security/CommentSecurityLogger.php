<?php

declare(strict_types=1);

namespace Blog\Application\Services\Security;

use Blog\Application\Services\Security\SecurityLoggerInterface;
use Illuminate\Support\Str;

final readonly class CommentSecurityLogger
{
    public function __construct(
        private SecurityLoggerInterface $securityLogger
    ) {}

    /**
     * Log spam attempt.
     */
    public function logSpamAttempt(
        string $ipAddress,
        string $content,
        ?int $userId = null,
        ?string $email = null
    ): void {
        $this->securityLogger->logSecurityEvent(
            event: 'comment_spam_attempt',
            severity: 'warning',
            ipAddress: $ipAddress,
            userId: $userId,
            metadata: [
                'content_preview' => Str::limit($content, 100),
                'content_length' => strlen($content),
                'email' => $email,
                'timestamp' => now()->toIso8601String()
            ]
        );
    }

    /**
     * Log rate limit exceeded.
     */
    public function logRateLimitExceeded(
        string $ipAddress,
        ?int $userId = null
    ): void {
        $this->securityLogger->logSecurityEvent(
            event: 'comment_rate_limit_exceeded',
            severity: 'warning',
            ipAddress: $ipAddress,
            userId: $userId,
            metadata: [
                'limit' => '3 per minute (production), 15 per minute (development)',
                'environment' => app()->environment(),
                'timestamp' => now()->toIso8601String()
            ]
        );
    }

    /**
     * Log suspicious content detected.
     */
    public function logSuspiciousContent(
        string $ipAddress,
        string $reason,
        string $content,
        ?int $userId = null,
        ?string $email = null
    ): void {
        $this->securityLogger->logSecurityEvent(
            event: 'comment_suspicious_content',
            severity: 'warning',
            ipAddress: $ipAddress,
            userId: $userId,
            metadata: [
                'reason' => $reason,
                'content_preview' => Str::limit($content, 100),
                'content_length' => strlen($content),
                'email' => $email,
                'timestamp' => now()->toIso8601String()
            ]
        );
    }

    /**
     * Log Cloudflare Turnstile validation failure.
     */
    public function logTurnstileFailure(
        string $ipAddress,
        ?string $email = null
    ): void {
        $this->securityLogger->logSecurityEvent(
            event: 'comment_turnstile_failure',
            severity: 'warning',
            ipAddress: $ipAddress,
            userId: null,
            metadata: [
                'email' => $email,
                'timestamp' => now()->toIso8601String()
            ]
        );
    }

    /**
     * Log multiple rejections from same IP/email.
     */
    public function logMultipleRejections(
        string $ipAddress,
        int $rejectionCount,
        ?string $email = null
    ): void {
        $this->securityLogger->logSecurityEvent(
            event: 'comment_multiple_rejections',
            severity: 'high',
            ipAddress: $ipAddress,
            userId: null,
            metadata: [
                'rejection_count' => $rejectionCount,
                'email' => $email,
                'timestamp' => now()->toIso8601String(),
                'recommendation' => 'Consider IP blocking if count exceeds threshold'
            ]
        );
    }

    /**
     * Log comment deletion (for audit trail).
     */
    public function logCommentDeletion(
        string $commentId,
        int $moderatorId,
        string $reason = 'Manual deletion'
    ): void {
        $this->securityLogger->logSecurityEvent(
            event: 'comment_deleted',
            severity: 'info',
            ipAddress: request()->ip() ?? 'unknown',
            userId: $moderatorId,
            metadata: [
                'comment_id' => $commentId,
                'reason' => $reason,
                'timestamp' => now()->toIso8601String()
            ]
        );
    }

    /**
     * Log bulk moderation action.
     */
    public function logBulkModerationAction(
        string $action,
        int $moderatorId,
        int $affectedCount,
        array $commentIds
    ): void {
        $this->securityLogger->logSecurityEvent(
            event: "comment_bulk_{$action}",
            severity: 'info',
            ipAddress: request()->ip() ?? 'unknown',
            userId: $moderatorId,
            metadata: [
                'action' => $action,
                'affected_count' => $affectedCount,
                'comment_ids' => array_slice($commentIds, 0, 10), // Limit to first 10 for log size
                'total_ids' => count($commentIds),
                'timestamp' => now()->toIso8601String()
            ]
        );
    }
}
