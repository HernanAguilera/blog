<?php

declare(strict_types=1);

namespace App\src\Application\Services\Security;

interface IPBlockServiceInterface
{
    /**
     * Record a failed attempt for an IP
     */
    public function recordFailedAttempt(string $ip, string $type = 'login'): void;

    /**
     * Check if an IP is blocked
     */
    public function isBlocked(string $ip): bool;

    /**
     * Get remaining block time in minutes
     */
    public function getRemainingBlockTime(string $ip): int;

    /**
     * Get current attempt count for IP
     */
    public function getAttemptCount(string $ip, string $type = 'login'): int;

    /**
     * Block an IP manually
     */
    public function blockIP(string $ip, int $minutes = 60, string $reason = 'Manual block'): void;

    /**
     * Unblock an IP
     */
    public function unblockIP(string $ip): bool;

    /**
     * Clear all attempts for an IP
     */
    public function clearAttempts(string $ip, string $type = 'login'): void;

    /**
     * Check if IP is whitelisted
     */
    public function isWhitelisted(string $ip): bool;

    /**
     * Get all currently blocked IPs
     */
    public function getBlockedIPs(): array;
}