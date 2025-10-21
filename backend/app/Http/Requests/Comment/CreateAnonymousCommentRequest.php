<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class CreateAnonymousCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anonymous users can comment
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'author_name' => [
                'required',
                'string',
                'min:2',
                'max:100'
            ],
            'author_email' => [
                'required',
                'email',
                'max:255'
            ],
            'author_website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'content' => [
                'required',
                'string',
                'min:3',
                'max:5000'
            ],
            'parent_id' => [
                'nullable',
                'uuid',
                'exists:comments,id'
            ],
            'cf-turnstile-response' => [
                'nullable',
                'string'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'author_name.required' => 'El nombre es requerido',
            'author_name.min' => 'El nombre debe tener al menos 2 caracteres',
            'author_name.max' => 'El nombre no puede exceder 100 caracteres',
            'author_email.required' => 'El email es requerido',
            'author_email.email' => 'Debe proporcionar un email válido',
            'author_website.url' => 'El sitio web debe ser una URL válida',
            'content.required' => 'El contenido del comentario es requerido',
            'content.min' => 'El comentario debe tener al menos 3 caracteres',
            'content.max' => 'El comentario no puede exceder 5000 caracteres',
            'parent_id.uuid' => 'El ID del comentario padre debe ser un UUID válido',
            'parent_id.exists' => 'El comentario padre no existe',
            'cf-turnstile-response.required' => 'La verificación de seguridad es requerida'
        ];
    }

    /**
     * Get the IP address
     */
    public function getIpAddress(): string
    {
        return $this->ip();
    }

    /**
     * Get the user agent
     */
    public function getUserAgent(): string
    {
        return $this->userAgent() ?? 'Unknown';
    }

    /**
     * Get the turnstile token
     */
    public function getTurnstileToken(): ?string
    {
        return $this->input('cf-turnstile-response');
    }
}
