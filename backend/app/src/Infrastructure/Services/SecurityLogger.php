<?php

declare(strict_types=1);

namespace App\src\Infrastructure\Services;

use App\src\Application\Services\Security\SecurityLoggerInterface;
use Illuminate\Support\Facades\Log;

final class SecurityLogger implements SecurityLoggerInterface
{
    private const SECURITY_CHANNEL = 'security';

    public function logFailedLogin(string $email, string $ip, ?string $reason = null): void
    {
        Log::channel(self::SECURITY_CHANNEL)->warning('Failed login attempt', [
            'event' => 'failed_login',
            'email' => $email,
            'ip' => $ip,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    public function logFailedOAuth(string $provider, string $ip, ?string $reason = null): void
    {
        Log::channel(self::SECURITY_CHANNEL)->warning('Failed OAuth attempt', [
            'event' => 'failed_oauth',
            'provider' => $provider,
            'ip' => $ip,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    public function logFailedTurnstile(string $ip, ?string $reason = null): void
    {
        Log::channel(self::SECURITY_CHANNEL)->warning('Failed Turnstile verification', [
            'event' => 'failed_turnstile',
            'ip' => $ip,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    public function logSuccessfulLogin(string $email, string $ip): void
    {
        Log::channel(self::SECURITY_CHANNEL)->info('Successful login', [
            'event' => 'successful_login',
            'email' => $email,
            'ip' => $ip,
            'timestamp' => now()->toISOString(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    public function logIPBlocked(string $ip, int $attempts, string $reason): void
    {
        Log::channel(self::SECURITY_CHANNEL)->error('IP blocked', [
            'event' => 'ip_blocked',
            'ip' => $ip,
            'attempts' => $attempts,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function logIPUnblocked(string $ip, ?string $unblockedBy = null): void
    {
        Log::channel(self::SECURITY_CHANNEL)->info('IP unblocked', [
            'event' => 'ip_unblocked',
            'ip' => $ip,
            'unblocked_by' => $unblockedBy,
            'timestamp' => now()->toISOString(),
        ]);
    }
}