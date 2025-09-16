<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by middleware or controller
        return true;
    }

    public function rules(): array
    {
        $postId = $this->route('id');

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('posts', 'slug')->ignore($postId)
            ],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'excerpt.max' => 'El extracto no puede exceder 500 caracteres.',
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'slug.unique' => 'Este slug ya está en uso.',
            'status.in' => 'El estado debe ser: draft, published o archived.',
            'meta_description.max' => 'La meta descripción no puede exceder 160 caracteres.',
            'scheduled_at.date' => 'La fecha de programación debe ser válida.',
        ];
    }
}