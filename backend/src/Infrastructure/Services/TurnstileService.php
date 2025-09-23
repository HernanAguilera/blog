<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Services;

use Blog\Application\Services\Security\TurnstileServiceInterface;
use Coderflex\LaravelTurnstile\Facades\LaravelTurnstile;

final class TurnstileService implements TurnstileServiceInterface
{
    public function verify(string $token, ?string $ip = null): bool
    {
        if (!$this->isEnabled()) {
            // If Turnstile is not configured, allow the request
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            return LaravelTurnstile::validate($token, $ip);
        } catch (\Exception $e) {
            // Log the error but don't fail the request if Turnstile service is down
            \Log::warning('Turnstile verification failed', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
                'ip' => $ip
            ]);

            // In case of service failure, allow the request but log it
            return true;
        }
    }

    public function isEnabled(): bool
    {
        $siteKey = config('turnstile.turnstile_site_key');
        $secretKey = config('turnstile.turnstile_secret_key');

        return !empty($siteKey) && !empty($secretKey);
    }

    public function getSiteKey(): ?string
    {
        return config('turnstile.turnstile_site_key');
    }
}