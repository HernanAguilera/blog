<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
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
            'slug' => ['sometimes', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
            'status' => ['sometimes', 'string', 'in:draft,published'],
            'translations' => ['sometimes', 'array', 'min:1'],
            'translations.*.title' => ['required_with:translations', 'string', 'max:255'],
            'translations.*.content' => ['required_with:translations', 'string'],
            'translations.*.meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones',
            'status.in' => 'El estado debe ser draft o published',
            'translations.*.title.required_with' => 'El título es obligatorio',
            'translations.*.content.required_with' => 'El contenido es obligatorio',
        ];
    }
}
