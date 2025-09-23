<?php

declare(strict_types=1);

namespace Blog\Interface\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutoSavePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $maxContentSize = config('editor.max_content_size', 1048576); // 1MB default

        return [
            'post_id' => 'nullable|integer|exists:posts,id',
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
            'scheduled_at' => 'nullable|date|after:now'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required for auto-save',
            'content.required' => 'Content is required for auto-save',
            'content.max' => 'Content exceeds maximum allowed size',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens',
            'featured_image.url' => 'Featured image must be a valid URL',
            'scheduled_at.after' => 'Scheduled date must be in the future'
        ];
    }

    public function passedValidation(): void
    {
        // Rate limiting check
        $rateLimitKey = 'autosave:' . $this->user()->id;
        $maxAttempts = config('editor.autosave_rate_limit', 30); // 30 per minute

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($rateLimitKey);
            abort(429, "Too many auto-save attempts. Try again in {$seconds} seconds.");
        }

        \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 60); // 1 minute window
    }
}