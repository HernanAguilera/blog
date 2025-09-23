<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Blog\Domain\User\ValueObjects\SocialProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialAuthRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $enabledProviders = array_map(
            fn(SocialProvider $provider) => $provider->value,
            SocialProvider::getEnabledProviders()
        );

        return [
            'provider' => [
                'required',
                'string',
                Rule::in($enabledProviders)
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'provider.required' => 'Social provider is required',
            'provider.in' => 'The selected social provider is not supported or enabled',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'provider' => 'social provider',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Get provider from route parameter
        $this->merge([
            'provider' => $this->route('provider')
        ]);
    }
}