<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Middleware;

use App\src\Domain\Post\Services\HtmlSanitizerInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SanitizeHtmlInput
{
    public function __construct(
        private HtmlSanitizerInterface $htmlSanitizer
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Only process if enabled in config
        if (!config('editor.features.enable_detailed_logging', true)) {
            return $next($request);
        }

        // Only sanitize specific content fields to avoid over-sanitizing
        $fieldsToSanitize = ['content', 'excerpt', 'meta_description'];
        $sanitized = false;
        $originalData = [];

        foreach ($fieldsToSanitize as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $originalValue = $request->input($field);

                // Skip if already clean (performance optimization)
                if ($this->htmlSanitizer->isContentSafe($originalValue)) {
                    continue;
                }

                $originalData[$field] = $originalValue;

                // Sanitize the content
                $sanitizedContent = $this->htmlSanitizer->sanitize($originalValue);

                // Replace in request
                $request->merge([$field => (string) $sanitizedContent]);
                $sanitized = true;
            }
        }

        // Log sanitization activity for security monitoring
        if ($sanitized && config('editor.security.log_suspicious_activity', true)) {
            Log::info('HTML content sanitized in request', [
                'route' => $request->route()?->getName(),
                'user_id' => $request->user()?->id,
                'fields_sanitized' => array_keys($originalData),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'sanitization_applied' => true
            ]);
        }

        $response = $next($request);

        // Add security headers to response if content was sanitized
        if ($sanitized) {
            $response->headers->set('X-Content-Sanitized', 'true');
            $response->headers->set('X-Sanitization-Applied', implode(',', array_keys($originalData)));
        }

        return $response;
    }

    /**
     * Determine if the content is potentially dangerous
     */
    private function isContentPotentiallyDangerous(string $content): bool
    {
        // Simple check for common dangerous patterns
        $dangerousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/data:text\/html/i',
            '/onload\s*=/i',
            '/onclick\s*=/i',
            '/onerror\s*=/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log detailed security information about dangerous content
     */
    private function logSecurityIncident(Request $request, string $field, string $content): void
    {
        Log::warning('Potentially dangerous HTML content detected', [
            'route' => $request->route()?->getName(),
            'field' => $field,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_length' => strlen($content),
            'content_preview' => substr($content, 0, 200),
            'timestamp' => now()->toISOString(),
            'severity' => 'medium'
        ]);
    }
}