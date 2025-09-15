<?php

namespace App\Providers;

use App\src\Application\Services\Auth\SocialAuthServiceInterface;
use App\src\Application\Services\Security\TurnstileServiceInterface;
use App\src\Application\Services\Security\SecurityLoggerInterface;
use App\src\Application\Services\Security\IPBlockServiceInterface;
use App\src\Infrastructure\Services\SocialAuthService;
use App\src\Infrastructure\Services\TurnstileService;
use App\src\Infrastructure\Services\SecurityLogger;
use App\src\Infrastructure\Services\IPBlockService;
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

        // General API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
