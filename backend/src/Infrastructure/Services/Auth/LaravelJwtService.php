<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Services\Auth;

use Blog\Application\Services\Auth\JwtServiceInterface;
use Blog\Domain\User\Entities\User;
use DateTimeImmutable;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

final class LaravelJwtService implements JwtServiceInterface
{
    public function generateToken(User $user): string
    {
        // Find the actual Eloquent user from database
        $eloquentUser = \App\Models\User::find($user->getId()->value());

        if (!$eloquentUser) {
            throw new \Exception('User not found in database');
        }

        $customClaims = [
            'user_id' => $user->getId()->value(),
            'email' => $user->getEmail()->value(),
            'role' => $user->getRole()->value,
        ];

        return JWTAuth::claims($customClaims)->fromUser($eloquentUser);
    }

    public function generateRefreshToken(User $user): string
    {
        $customClaims = [
            'user_id' => $user->getId()->value(),
            'type' => 'refresh',
        ];

        // Create an Eloquent user model for JWT generation
        $eloquentUser = new \App\Models\User([
            'id' => $user->getId()->value(),
            'email' => $user->getEmail()->value(),
        ]);
        $eloquentUser->exists = true;

        return JWTAuth::claims($customClaims)
            ->setTTL(config('jwt.refresh_ttl', 20160))
            ->fromUser($eloquentUser);
    }

    public function validateToken(string $token): ?int
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            return $payload->get('user_id');
        } catch (JWTException $e) {
            return null;
        }
    }

    public function validateRefreshToken(string $refreshToken): ?int
    {
        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            if ($payload->get('type') !== 'refresh') {
                return null;
            }

            return $payload->get('user_id');
        } catch (JWTException $e) {
            return null;
        }
    }

    public function invalidateToken(string $token): bool
    {
        try {
            JWTAuth::setToken($token)->invalidate();
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }

    public function invalidateAllUserTokens(int $userId): bool
    {
        try {
            JWTAuth::invalidate();
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }

    public function isTokenBlacklisted(string $token): bool
    {
        try {
            JWTAuth::setToken($token)->checkOrFail();
            return false;
        } catch (JWTException $e) {
            return true;
        }
    }

    public function getTokenTtl(string $token): ?int
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $exp = $payload->get('exp');
            $now = time();

            return max(0, $exp - $now);
        } catch (JWTException $e) {
            return null;
        }
    }

    public function getTokenPayload(string $token): ?array
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();

            return [
                'user_id' => $payload->get('user_id'),
                'email' => $payload->get('email'),
                'role' => $payload->get('role'),
                'iat' => $payload->get('iat'),
                'exp' => $payload->get('exp'),
            ];
        } catch (JWTException $e) {
            return null;
        }
    }

    public function refreshAccessToken(string $refreshToken): ?string
    {
        try {
            $userId = $this->validateRefreshToken($refreshToken);
            if (!$userId) {
                return null;
            }

            $newToken = JWTAuth::setToken($refreshToken)->refresh();
            return $newToken;
        } catch (JWTException $e) {
            return null;
        }
    }

    public function getConfig(): array
    {
        return [
            'access_ttl' => config('jwt.ttl', 60),
            'refresh_ttl' => config('jwt.refresh_ttl', 20160),
            'algorithm' => config('jwt.algo', 'HS256'),
            'issuer' => config('app.name', 'BlogV2'),
        ];
    }

    public function extractTokenFromHeader(?string $authHeader): ?string
    {
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        return substr($authHeader, 7);
    }

    public function getTokenExpiration(string $token): DateTimeImmutable
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $exp = $payload->get('exp');

            return new DateTimeImmutable('@' . $exp);
        } catch (JWTException $e) {
            return new DateTimeImmutable('+1 hour');
        }
    }
}