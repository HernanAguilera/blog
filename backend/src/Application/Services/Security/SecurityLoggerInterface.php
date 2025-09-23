<?php

declare(strict_types=1);

namespace Blog\Application\Services\Security;

interface SecurityLoggerInterface
{
    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $email, string $ip, ?string $reason = null): void;

    /**
     * Log failed OAuth attempt
     */
    public function logFailedOAuth(string $provider, string $ip, ?string $reason = null): void;

    /**
     * Log failed Turnstile verification
     */
    public function logFailedTurnstile(string $ip, ?string $reason = null): void;

    /**
     * Log successful authentication
     */
    public function logSuccessfulLogin(string $email, string $ip): void;

    /**
     * Log IP block event
     */
    public function logIPBlocked(string $ip, int $attempts, string $reason): void;

    /**
     * Log IP unblock event
     */
    public function logIPUnblocked(string $ip, ?string $unblockedBy = null): void;
}