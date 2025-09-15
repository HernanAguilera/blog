<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\src\Application\UseCases\Auth\LoginUserUseCase;
use App\src\Application\UseCases\Auth\RegisterUserUseCase;
use App\src\Application\UseCases\Auth\LogoutUserUseCase;
use App\src\Application\DTOs\Auth\LoginUserDTO;
use App\src\Application\DTOs\Auth\RegisterUserDTO;
use App\src\Application\Services\Security\SecurityLoggerInterface;
use App\src\Application\Services\Security\IPBlockServiceInterface;
use App\src\Domain\User\Exceptions\UserNotFoundException;
use App\src\Domain\User\Exceptions\UserAlreadyExistsException;
use App\src\Domain\User\Exceptions\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginUserUseCase $loginUserUseCase,
        private readonly RegisterUserUseCase $registerUserUseCase,
        private readonly LogoutUserUseCase $logoutUserUseCase,
        private readonly SecurityLoggerInterface $securityLogger,
        private readonly IPBlockServiceInterface $ipBlockService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $ip = $request->ip();

        // Check if IP is blocked
        if ($this->ipBlockService->isBlocked($ip)) {
            $remainingTime = $this->ipBlockService->getRemainingBlockTime($ip);

            return response()->json([
                'success' => false,
                'message' => 'IP temporarily blocked due to too many failed attempts',
                'errors' => ['ip_blocked' => ["IP blocked for {$remainingTime} more minutes"]]
            ], 429);
        }

        try {
            $dto = new LoginUserDTO(
                email: $email,
                password: $request->validated('password')
            );

            $result = $this->loginUserUseCase->execute($dto);

            // Log successful login
            $this->securityLogger->logSuccessfulLogin($email, $ip);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $result->token,
                    'expires_at' => $result->expiresAt->format('Y-m-d H:i:s'),
                    'user' => [
                        'id' => $result->user->getId()->value(),
                        'name' => $result->user->getName(),
                        'email' => $result->user->getEmail()->value(),
                        'role' => $result->user->getRole()->value,
                        'is_active' => $result->user->isActive(),
                        'email_verified' => $result->user->isEmailVerified(),
                    ]
                ]
            ]);

        } catch (UserNotFoundException $e) {
            // Log failed login and record attempt
            $this->securityLogger->logFailedLogin($email, $ip, 'User not found');
            $this->ipBlockService->recordFailedAttempt($ip, 'login');

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => ['email' => ['The provided credentials are incorrect.']]
            ], 401);

        } catch (AuthenticationException $e) {
            // Log failed login and record attempt
            $this->securityLogger->logFailedLogin($email, $ip, $e->getMessage());
            $this->ipBlockService->recordFailedAttempt($ip, 'login');

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => ['auth' => [$e->getMessage()]]
            ], $e->getCode());

        } catch (\Exception $e) {
            // Log failed login and record attempt for unexpected errors
            $this->securityLogger->logFailedLogin($email, $ip, 'Unexpected error: ' . $e->getMessage());
            $this->ipBlockService->recordFailedAttempt($ip, 'login');

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'errors' => ['server' => ['Internal server error']]
            ], 500);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = new RegisterUserDTO(
                name: $request->validated('name'),
                email: $request->validated('email'),
                password: $request->validated('password')
            );

            $result = $this->registerUserUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'token' => $result->token,
                    'expires_at' => $result->expiresAt->format('Y-m-d H:i:s'),
                    'user' => [
                        'id' => $result->user->getId()->value(),
                        'name' => $result->user->getName(),
                        'email' => $result->user->getEmail()->value(),
                        'role' => $result->user->getRole()->value,
                        'is_active' => $result->user->isActive(),
                        'email_verified' => $result->user->isEmailVerified(),
                    ]
                ]
            ], 201);

        } catch (UserAlreadyExistsException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User already exists',
                'errors' => ['email' => ['A user with this email already exists.']]
            ], 409);

        } catch (\Exception $e) {
            // Temporary debug - log the actual error
            error_log('Registration Error: ' . $e->getMessage());
            error_log('Registration Error Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration',
                'errors' => ['server' => ['Internal server error']],
                'debug' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not provided',
                    'errors' => ['token' => ['Bearer token is required']]
                ], 401);
            }

            $success = $this->logoutUserUseCase->execute($token);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to logout',
                'errors' => ['token' => ['Invalid or expired token']]
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'errors' => ['server' => ['Internal server error']]
            ], 500);
        }
    }
}
