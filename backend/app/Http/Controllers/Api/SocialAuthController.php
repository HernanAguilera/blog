<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Blog\Application\DTOs\Auth\SocialLoginDTO;
use Blog\Application\UseCases\Auth\SocialLoginUseCase;
use Blog\Domain\User\Exceptions\AuthenticationException;
use Blog\Domain\User\ValueObjects\SocialProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SocialAuthController extends Controller
{
    public function __construct(
        private readonly SocialLoginUseCase $socialLoginUseCase
    ) {}

    /**
     * Redirect to social provider authentication page
     */
    public function redirect(string $provider): JsonResponse
    {
        try {
            // Validate provider
            $socialProvider = SocialProvider::fromString($provider);

            if (!$socialProvider->isEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => "Social provider '{$provider}' is not enabled",
                    'errors' => ['provider' => ["Provider '{$provider}' is not configured"]]
                ], 400);
            }

            $dto = SocialLoginDTO::fromProvider($provider);
            $redirectUrl = $this->socialLoginUseCase->getRedirectUrl($dto);

            return response()->json([
                'success' => true,
                'data' => [
                    'redirect_url' => $redirectUrl,
                    'provider' => $provider
                ]
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid social provider',
                'errors' => ['provider' => [$e->getMessage()]]
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to redirect to social provider',
                'errors' => ['redirect' => ['Failed to generate redirect URL']]
            ], 500);
        }
    }

    /**
     * Handle callback from social provider
     */
    public function callback(string $provider, Request $request): JsonResponse
    {
        try {
            // Validate provider
            $socialProvider = SocialProvider::fromString($provider);

            if (!$socialProvider->isEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => "Social provider '{$provider}' is not enabled",
                    'errors' => ['provider' => ["Provider '{$provider}' is not configured"]]
                ], 400);
            }

            // Check for OAuth errors in callback
            if ($request->has('error')) {
                $error = $request->get('error');
                $errorDescription = $request->get('error_description', 'Authentication was cancelled or failed');

                return response()->json([
                    'success' => false,
                    'message' => 'Social authentication failed',
                    'errors' => ['oauth' => [$errorDescription]]
                ], 400);
            }

            $dto = SocialLoginDTO::fromProvider($provider);
            $result = $this->socialLoginUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'data' => [
                    'token' => $result->token,
                    'expires_at' => $result->expiresAt->format('Y-m-d H:i:s'),
                    'user' => [
                        'id' => $result->user->getId()?->value(),
                        'name' => $result->user->getName(),
                        'email' => $result->user->getEmail()->value(),
                        'role' => $result->user->getRole()->value,
                        'is_active' => $result->user->isActive(),
                        'social_provider' => $result->user->getSocialProvider()?->value,
                        'email_verified_at' => $result->user->getEmailVerifiedAt()?->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid social provider',
                'errors' => ['provider' => [$e->getMessage()]]
            ], 400);
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'errors' => ['auth' => [$e->getMessage()]]
            ], 401);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Social authentication error',
                'errors' => ['social' => [$e->getMessage()]]
            ], 400);
        } catch (\Exception $e) {
            // Log unexpected errors for debugging
            \Log::error('Social authentication error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error during authentication',
                'errors' => ['server' => ['An unexpected error occurred']]
            ], 500);
        }
    }

    /**
     * Get available social providers
     */
    public function providers(): JsonResponse
    {
        try {
            $enabledProviders = SocialProvider::getEnabledProviders();

            $providers = array_map(function (SocialProvider $provider) {
                return [
                    'name' => $provider->value,
                    'display_name' => $provider->getDisplayName(),
                    'enabled' => true
                ];
            }, $enabledProviders);

            return response()->json([
                'success' => true,
                'data' => [
                    'providers' => $providers,
                    'total' => count($providers)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve social providers',
                'errors' => ['providers' => ['Failed to load provider configuration']]
            ], 500);
        }
    }
}