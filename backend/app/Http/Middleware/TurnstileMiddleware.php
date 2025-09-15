<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\src\Application\Services\Security\TurnstileServiceInterface;
use App\src\Application\Services\Security\SecurityLoggerInterface;
use App\src\Application\Services\Security\IPBlockServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TurnstileMiddleware
{
    public function __construct(
        private readonly TurnstileServiceInterface $turnstileService,
        private readonly SecurityLoggerInterface $securityLogger,
        private readonly IPBlockServiceInterface $ipBlockService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip validation if Turnstile is not enabled
        if (!$this->turnstileService->isEnabled()) {
            return $next($request);
        }

        // Only validate POST requests (forms)
        if (!$request->isMethod('POST')) {
            return $next($request);
        }

        // Get Turnstile token from request
        $turnstileToken = $request->input('cf-turnstile-response');
        $ip = $request->ip();

        if (empty($turnstileToken)) {
            $this->securityLogger->logFailedTurnstile($ip, 'Missing CAPTCHA token');
            $this->ipBlockService->recordFailedAttempt($ip, 'turnstile');

            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA verification is required',
                'errors' => ['turnstile' => ['CAPTCHA token is missing']]
            ], 400);
        }

        // Verify the token
        if (!$this->turnstileService->verify($turnstileToken, $ip)) {
            $this->securityLogger->logFailedTurnstile($ip, 'CAPTCHA verification failed');
            $this->ipBlockService->recordFailedAttempt($ip, 'turnstile');

            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA verification failed',
                'errors' => ['turnstile' => ['CAPTCHA verification failed. Please try again.']]
            ], 400);
        }

        return $next($request);
    }
}
