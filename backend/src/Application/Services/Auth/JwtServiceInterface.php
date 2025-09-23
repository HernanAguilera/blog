<?php

declare(strict_types=1);

namespace Blog\Application\Services\Auth;

use Blog\Domain\User\Entities\User;

interface JwtServiceInterface
{
    /**
     * Generate a JWT token for the given user.
     */
    public function generateToken(User $user): string;

    /**
     * Generate a refresh token for the given user.
     */
    public function generateRefreshToken(User $user): string;

    /**
     * Validate and parse a JWT token.
     * Returns the user ID if valid, null if invalid.
     */
    public function validateToken(string $token): ?int;

    /**
     * Validate a refresh token.
     * Returns the user ID if valid, null if invalid.
     */
    public function validateRefreshToken(string $refreshToken): ?int;

    /**
     * Invalidate a token (add to blacklist).
     */
    public function invalidateToken(string $token): bool;

    /**
     * Invalidate all tokens for a user.
     */
    public function invalidateAllUserTokens(int $userId): bool;

    /**
     * Check if a token is blacklisted.
     */
    public function isTokenBlacklisted(string $token): bool;

    /**
     * Get the remaining time until token expiration in seconds.
     */
    public function getTokenTtl(string $token): ?int;

    /**
     * Get the payload data from a valid token.
     *
     * @return array{user_id: int, email: string, role: string, iat: int, exp: int}|null
     */
    public function getTokenPayload(string $token): ?array;

    /**
     * Refresh an access token using a refresh token.
     * Returns new access token if valid, null if invalid.
     */
    public function refreshAccessToken(string $refreshToken): ?string;

    /**
     * Get token configuration.
     *
     * @return array{access_ttl: int, refresh_ttl: int, algorithm: string, issuer: string}
     */
    public function getConfig(): array;

    /**
     * Extract token from Authorization header.
     * Expected format: "Bearer {token}"
     */
    public function extractTokenFromHeader(?string $authHeader): ?string;

    /**
     * Get token expiration date.
     */
    public function getTokenExpiration(string $token): \DateTimeImmutable;
}