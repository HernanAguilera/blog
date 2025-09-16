<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreviewPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $maxContentSize = config('editor.max_content_size', 1048576); // 1MB default
        $maxTtlHours = config('editor.max_preview_ttl_hours', 168); // 1 week default

        return [
            'title' => 'required|string|max:255',
            'content' => [
                'required',
                'string',
                'max:' . $maxContentSize
            ],
            'excerpt' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9-]+$/',
            'status' => 'nullable|in:draft,published,archived',
            'featured_image' => 'nullable|string|max:255|url',
            'meta_description' => 'nullable|string|max:160',
            'scheduled_at' => 'nullable|date|after:now',
            'ttl_hours' => [
                'nullable',
                'integer',
                'min:1',
                'max:' . $maxTtlHours
            ]
        ];
    }

    public function messages(): array
    {
        $maxTtlHours = config('editor.max_preview_ttl_hours', 168);

        return [
            'title.required' => 'Title is required for preview generation',
            'content.required' => 'Content is required for preview generation',
            'content.max' => 'Content exceeds maximum allowed size',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens',
            'featured_image.url' => 'Featured image must be a valid URL',
            'scheduled_at.after' => 'Scheduled date must be in the future',
            'ttl_hours.max' => "Preview TTL cannot exceed {$maxTtlHours} hours"
        ];
    }

    public function passedValidation(): void
    {
        // Rate limiting check for preview generation
        $rateLimitKey = 'preview:' . $this->user()->id;
        $maxAttempts = config('editor.preview_rate_limit', 5); // 5 per minute

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($rateLimitKey);
            abort(429, "Too many preview generation attempts. Try again in {$seconds} seconds.");
        }

        \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 60); // 1 minute window
    }
}