<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Blog\Domain\Post\ValueObjects\PostLocale;

class CreatePostTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $supportedLocales = implode(',', PostLocale::supportedLocales());

        return [
            'locale' => ['required', 'string', "in:{$supportedLocales}"],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'slug' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'content' => ['required', 'string', 'min:10'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'locale.required' => 'El idioma es requerido',
            'locale.in' => 'Idioma no soportado',
            'title.required' => 'El título es requerido',
            'title.min' => 'El título debe tener al menos 3 caracteres',
            'title.max' => 'El título no puede exceder 255 caracteres',
            'slug.required' => 'El slug es requerido',
            'slug.regex' => 'El slug debe contener solo letras minúsculas, números y guiones',
            'content.required' => 'El contenido es requerido',
            'content.min' => 'El contenido debe tener al menos 10 caracteres',
            'excerpt.max' => 'El extracto no puede exceder 500 caracteres',
            'meta_description.max' => 'La meta descripción no puede exceder 160 caracteres',
        ];
    }
}
