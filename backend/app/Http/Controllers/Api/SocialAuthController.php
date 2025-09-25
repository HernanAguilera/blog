<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Blog\Application\DTOs\Auth\SocialLoginDTO;
use Blog\Application\UseCases\Auth\SocialLoginUseCase;
use Blog\Domain\User\Exceptions\AuthenticationException;
use Blog\Domain\User\ValueObjects\SocialProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
    public function redirect(string $provider, Request $request): RedirectResponse
    {
        try {
            // Store return URL in session for later use
            $returnUrl = $request->query('return_url', config('app.frontend_url', 'http://localhost:3000'));
            session(['oauth_return_url' => $returnUrl]);

            // Validate provider
            $socialProvider = SocialProvider::fromString($provider);

            if (!$socialProvider->isEnabled()) {
                return redirect($returnUrl . '?error=provider_not_enabled&message=' . urlencode("Provider '{$provider}' is not configured"));
            }

            $dto = SocialLoginDTO::fromProvider($provider);
            $redirectUrl = $this->socialLoginUseCase->getRedirectUrl($dto);

            return redirect($redirectUrl);

        } catch (\InvalidArgumentException $e) {
            $returnUrl = session('oauth_return_url', config('app.frontend_url', 'http://localhost:3000'));
            return redirect($returnUrl . '?error=invalid_provider&message=' . urlencode($e->getMessage()));
        } catch (\Exception $e) {
            $returnUrl = session('oauth_return_url', config('app.frontend_url', 'http://localhost:3000'));
            return redirect($returnUrl . '?error=redirect_failed&message=' . urlencode('Unable to redirect to social provider'));
        }
    }

    /**
     * Handle callback from social provider
     */
    public function callback(string $provider, Request $request): RedirectResponse
    {
        $returnUrl = session('oauth_return_url', config('app.frontend_url', 'http://localhost:3000'));

        try {
            // Validate provider
            $socialProvider = SocialProvider::fromString($provider);

            if (!$socialProvider->isEnabled()) {
                return redirect($returnUrl . '?error=provider_not_enabled&message=' . urlencode("Provider '{$provider}' is not configured"));
            }

            // Check for OAuth errors in callback
            if ($request->has('error')) {
                $errorDescription = $request->get('error_description', 'Authentication was cancelled or failed');
                return redirect($returnUrl . '?error=oauth_error&message=' . urlencode($errorDescription));
            }

            $dto = SocialLoginDTO::fromProvider($provider);
            $result = $this->socialLoginUseCase->execute($dto);

            // Clear the return URL from session
            session()->forget('oauth_return_url');

            // Redirect to frontend with token and success flag
            return redirect($returnUrl . '?token=' . $result->token . '&auth=success');

        } catch (\InvalidArgumentException $e) {
            return redirect($returnUrl . '?error=invalid_provider&message=' . urlencode($e->getMessage()));
        } catch (AuthenticationException $e) {
            return redirect($returnUrl . '?error=auth_failed&message=' . urlencode($e->getMessage()));
        } catch (\RuntimeException $e) {
            return redirect($returnUrl . '?error=social_error&message=' . urlencode($e->getMessage()));
        } catch (\Exception $e) {
            // Log unexpected errors for debugging
            \Illuminate\Support\Facades\Log::error('Social authentication error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect($returnUrl . '?error=server_error&message=' . urlencode('An unexpected error occurred'));
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