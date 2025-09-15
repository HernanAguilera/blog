<?php

declare(strict_types=1);

namespace App\src\Infrastructure\Services;

use App\src\Application\Services\Security\IPBlockServiceInterface;
use App\src\Application\Services\Security\SecurityLoggerInterface;
use Illuminate\Support\Facades\Cache;

final class IPBlockService implements IPBlockServiceInterface
{
    private const ATTEMPT_PREFIX = 'ip_attempts:';
    private const BLOCK_PREFIX = 'ip_blocked:';
    private const WHITELIST_KEY = 'ip_whitelist';

    // Configuration constants
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const MAX_OAUTH_ATTEMPTS = 3;
    private const MAX_TURNSTILE_ATTEMPTS = 10;
    private const ATTEMPT_WINDOW_MINUTES = 15;
    private const BLOCK_DURATION_MINUTES = 60;

    public function __construct(
        private readonly SecurityLoggerInterface $securityLogger
    ) {}

    public function recordFailedAttempt(string $ip, string $type = 'login'): void
    {
        if ($this->isWhitelisted($ip)) {
            return;
        }

        $key = $this->getAttemptKey($ip, $type);
        $attempts = Cache::get($key, 0) + 1;

        Cache::put($key, $attempts, now()->addMinutes(self::ATTEMPT_WINDOW_MINUTES));

        $maxAttempts = $this->getMaxAttempts($type);

        if ($attempts >= $maxAttempts) {
            $this->blockIP($ip, self::BLOCK_DURATION_MINUTES, "Too many {$type} attempts: {$attempts}");
        }
    }

    public function isBlocked(string $ip): bool
    {
        if ($this->isWhitelisted($ip)) {
            return false;
        }

        $key = $this->getBlockKey($ip);
        return Cache::has($key);
    }

    public function getRemainingBlockTime(string $ip): int
    {
        if (!$this->isBlocked($ip)) {
            return 0;
        }

        $key = $this->getBlockKey($ip);
        $blockData = Cache::get($key);

        if (!$blockData || !isset($blockData['expires_at'])) {
            return 0;
        }

        $expiresAt = $blockData['expires_at'];
        $remaining = $expiresAt->diffInMinutes(now(), false);

        return max(0, (int) $remaining);
    }

    public function getAttemptCount(string $ip, string $type = 'login'): int
    {
        $key = $this->getAttemptKey($ip, $type);
        return (int) Cache::get($key, 0);
    }

    public function blockIP(string $ip, int $minutes = 60, string $reason = 'Manual block'): void
    {
        $key = $this->getBlockKey($ip);
        $expiresAt = now()->addMinutes($minutes);

        $blockData = [
            'ip' => $ip,
            'blocked_at' => now(),
            'expires_at' => $expiresAt,
            'reason' => $reason,
            'duration_minutes' => $minutes,
        ];

        Cache::put($key, $blockData, $expiresAt);

        $attempts = $this->getAttemptCount($ip);
        $this->securityLogger->logIPBlocked($ip, $attempts, $reason);
    }

    public function unblockIP(string $ip): bool
    {
        $key = $this->getBlockKey($ip);

        if (!Cache::has($key)) {
            return false;
        }

        Cache::forget($key);

        // Also clear all attempt counters for this IP
        $this->clearAllAttempts($ip);

        $this->securityLogger->logIPUnblocked($ip, 'manual');

        return true;
    }

    public function clearAttempts(string $ip, string $type = 'login'): void
    {
        $key = $this->getAttemptKey($ip, $type);
        Cache::forget($key);
    }

    public function isWhitelisted(string $ip): bool
    {
        $whitelist = Cache::get(self::WHITELIST_KEY, []);

        // Add default whitelisted IPs
        $defaultWhitelist = [
            '127.0.0.1',
            '::1',
            'localhost',
        ];

        $allWhitelisted = array_merge($defaultWhitelist, $whitelist);

        foreach ($allWhitelisted as $whitelistedIP) {
            if ($this->ipMatches($ip, $whitelistedIP)) {
                return true;
            }
        }

        return false;
    }

    public function getBlockedIPs(): array
    {
        // This is a simplified implementation
        // In a real scenario, you might want to use a more efficient method
        // to track all blocked IPs (like using a Redis set)

        $blockedIPs = [];
        $pattern = self::BLOCK_PREFIX . '*';

        // Note: This is not available in all cache drivers
        // For Redis, you could use SCAN or KEYS commands
        // For file/database cache, you'd need a different approach

        return $blockedIPs;
    }

    private function getAttemptKey(string $ip, string $type): string
    {
        return self::ATTEMPT_PREFIX . $type . ':' . $ip;
    }

    private function getBlockKey(string $ip): string
    {
        return self::BLOCK_PREFIX . $ip;
    }

    private function getMaxAttempts(string $type): int
    {
        return match ($type) {
            'login' => self::MAX_LOGIN_ATTEMPTS,
            'oauth' => self::MAX_OAUTH_ATTEMPTS,
            'turnstile' => self::MAX_TURNSTILE_ATTEMPTS,
            default => self::MAX_LOGIN_ATTEMPTS,
        };
    }

    private function clearAllAttempts(string $ip): void
    {
        $types = ['login', 'oauth', 'turnstile'];

        foreach ($types as $type) {
            $this->clearAttempts($ip, $type);
        }
    }

    private function ipMatches(string $ip, string $pattern): bool
    {
        // Simple exact match for now
        // Could be extended to support CIDR notation
        return $ip === $pattern;
    }
}