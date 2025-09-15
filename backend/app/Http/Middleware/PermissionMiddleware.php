<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\src\Domain\User\Entities\User;
use App\src\Domain\User\ValueObjects\Permission;
use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use ValueError;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        /** @var User|null $user */
        $user = $request->attributes->get('auth_user');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'errors' => ['auth' => ['User must be authenticated to access this resource']]
            ], 401);
        }

        try {
            $permissionEnum = Permission::from($permission);
        } catch (ValueError) {
            throw new InvalidArgumentException("Invalid permission: {$permission}");
        }

        if (!$user->can($permissionEnum)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => ['permission' => ["You don't have permission to {$permissionEnum->getDescription()}"]]
            ], 403);
        }

        return $next($request);
    }
}
