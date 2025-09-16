<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:posts,slug'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'content.required' => 'El contenido es obligatorio.',
            'excerpt.max' => 'El extracto no puede exceder 500 caracteres.',
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'slug.unique' => 'Este slug ya está en uso.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser: draft, published o archived.',
            'meta_description.max' => 'La meta descripción no puede exceder 160 caracteres.',
            'scheduled_at.date' => 'La fecha de programación debe ser válida.',
            'scheduled_at.after' => 'La fecha de programación debe ser futura.',
        ];
    }

    public function getAuthorId(): int
    {
        return auth()->id();
    }
}