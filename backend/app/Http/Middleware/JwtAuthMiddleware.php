<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Blog\Application\Services\Auth\JwtServiceInterface;
use Blog\Domain\User\Repositories\UserRepositoryInterface;
use Blog\Domain\User\ValueObjects\UserId;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    public function __construct(
        private readonly JwtServiceInterface $jwtService,
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided',
                'errors' => ['auth' => ['Bearer token is required']]
            ], 401);
        }

        $userId = $this->jwtService->validateToken($token);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
                'errors' => ['auth' => ['Token is invalid or expired']]
            ], 401);
        }

        if ($this->jwtService->isTokenBlacklisted($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token has been revoked',
                'errors' => ['auth' => ['Token has been blacklisted']]
            ], 401);
        }

        $user = $this->userRepository->findById(new UserId($userId));

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'errors' => ['auth' => ['Associated user not found']]
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'User account is not active',
                'errors' => ['auth' => ['User account has been deactivated']]
            ], 403);
        }

        // Add user and token to request for use in controllers
        $request->attributes->set('auth_user', $user);
        $request->attributes->set('auth_token', $token);

        // IMPORTANT: Set user in Laravel Auth context for FormRequest authorization
        // Load the Eloquent model for Laravel Auth compatibility
        $eloquentUser = \App\Models\User::find($user->getId()->value());
        if ($eloquentUser) {
            \Illuminate\Support\Facades\Auth::setUser($eloquentUser);
        }

        return $next($request);
    }
}
