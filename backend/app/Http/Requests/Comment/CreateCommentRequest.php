<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del comentario es requerido',
            'content.min' => 'El comentario debe tener al menos 3 caracteres',
            'content.max' => 'El comentario no puede exceder 5000 caracteres',
            'parent_id.uuid' => 'El ID del comentario padre debe ser un UUID válido',
            'parent_id.exists' => 'El comentario padre no existe'
        ];
    }

    /**
     * Get the user ID from the authenticated user
     */
    public function getUserId(): int
    {
        return Auth::id();
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
}
