<?php

namespace App\Providers;

use Blog\Application\Services\Auth\SocialAuthServiceInterface;
use Blog\Application\Services\Security\TurnstileServiceInterface;
use Blog\Application\Services\Security\SecurityLoggerInterface;
use Blog\Application\Services\Security\IPBlockServiceInterface;
use Blog\Infrastructure\Services\SocialAuthService;
use Blog\Infrastructure\Services\TurnstileService;
use Blog\Infrastructure\Services\SecurityLogger;
use Blog\Infrastructure\Services\IPBlockService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Social Authentication Service
        $this->app->bind(SocialAuthServiceInterface::class, SocialAuthService::class);

        // Register Turnstile Service
        $this->app->bind(TurnstileServiceInterface::class, TurnstileService::class);

        // Register Security Services
        $this->app->bind(SecurityLoggerInterface::class, SecurityLogger::class);
        $this->app->bind(IPBlockServiceInterface::class, IPBlockService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Login rate limiting: 30 per minute for development (5 for production)
        RateLimiter::for('login', function (Request $request) {
            $maxAttempts = app()->environment('production') ? 5 : 30;

            return Limit::perMinute($maxAttempts)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many login attempts. Please try again later.',
                        'errors' => ['rate_limit' => ['Too many attempts from this IP address']]
                    ], 429, $headers);
                });
        });

        // Social authentication rate limiting: 10 per minute
        RateLimiter::for('social-auth', function (Request $request) {
            $maxAttempts = app()->environment('production') ? 10 : 30;

            return Limit::perMinute($maxAttempts)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many social authentication attempts. Please try again later.',
                        'errors' => ['rate_limit' => ['Too many social auth attempts from this IP address']]
                    ], 429, $headers);
                });
        });

        // Posts rate limiting: 3 per minute for creation/modification
        RateLimiter::for('posts', function (Request $request) {
            $maxAttempts = app()->environment('production') ? 3 : 15;

            return Limit::perMinute($maxAttempts)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many post operations. Please try again later.',
                        'error' => 'Rate limit exceeded for post operations'
                    ], 429, $headers);
                });
        });

        // Auto-save rate limiting: 30 per minute (configurable)
        RateLimiter::for('autosave', function (Request $request) {
            $maxAttempts = config('editor.autosave_rate_limit', 30);

            return Limit::perMinute($maxAttempts)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many auto-save attempts. Please slow down.',
                        'error' => 'Auto-save rate limit exceeded'
                    ], 429, $headers);
                });
        });

        // Preview generation rate limiting: 5 per minute (configurable)
        RateLimiter::for('preview', function (Request $request) {
            $maxAttempts = config('editor.preview_rate_limit', 5);

            return Limit::perMinute($maxAttempts)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many preview generation attempts. Please wait before creating another preview.',
                        'error' => 'Preview generation rate limit exceeded'
                    ], 429, $headers);
                });
        });

        // Preview access rate limiting: 60 per minute (for public access)
        RateLimiter::for('preview-access', function (Request $request) {
            $maxAttempts = config('editor.preview_access_rate_limit', 60);

            return Limit::perMinute($maxAttempts)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many preview access attempts. Please try again later.',
                        'error' => 'Preview access rate limit exceeded'
                    ], 429, $headers);
                });
        });

        // Comments rate limiting: 3 per minute
        RateLimiter::for('comments', function (Request $request) {
            $maxAttempts = app()->environment('production') ? 3 : 15;

            return Limit::perMinute($maxAttempts)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many comments. Please wait before posting again.',
                        'errors' => ['rate_limit' => ['Maximum 3 comments per minute allowed']]
                    ], 429, $headers);
                });
        });

        // General API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
