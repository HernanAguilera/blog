<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;

class CreatePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/', 'unique:pages,slug'],
            'status' => ['required', 'string', 'in:draft,published'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.content' => ['required', 'string'],
            'translations.*.meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required' => 'El slug es obligatorio',
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones',
            'slug.unique' => 'Ya existe una página con este slug',
            'status.required' => 'El estado es obligatorio',
            'status.in' => 'El estado debe ser draft o published',
            'translations.required' => 'Debe proporcionar al menos una traducción',
            'translations.*.title.required' => 'El título es obligatorio',
            'translations.*.content.required' => 'El contenido es obligatorio',
        ];
    }
}
