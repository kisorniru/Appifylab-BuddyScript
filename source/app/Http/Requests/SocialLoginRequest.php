<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string'],
            'lastName' => ['nullable', 'string'],
            'email' => ['required', 'string', 'email'],
            'phone' => ['nullable', 'string'],
            'provider' => ['required', 'string'],
            'providerId' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => __('validation/SocialLoginRequest.firstName_required'),
            'firstName.string' => __('validation/SocialLoginRequest.firstName_string'),
            'lastName.string' => __('validation/SocialLoginRequest.lastName_string'),
            'email.required' => __('validation/SocialLoginRequest.email_required'),
            'email.string' => __('validation/SocialLoginRequest.email_string'),
            'email.email' => __('validation/SocialLoginRequest.email_email'),
            'phone.string' => __('validation/SocialLoginRequest.phone_string'),
            'provider.required' => __('validation/SocialLoginRequest.provider_required'),
            'provider.string' => __('validation/SocialLoginRequest.provider_string'),
            'providerId.required' => __('validation/SocialLoginRequest.providerId_required'),
            'providerId.string' => __('validation/SocialLoginRequest.providerId_string'),
        ];
    }

    public function attributes(): array
    {
        return [
            'firstName' => __('validation/SocialLoginRequest.firstName_attribute'),
            'lastName' => __('validation/SocialLoginRequest.lastName_attribute'),
            'email' => __('validation/SocialLoginRequest.email_attribute'),
            'phone' => __('validation/SocialLoginRequest.phone_attribute'),
            'provider' => __('validation/SocialLoginRequest.provider_attribute'),
            'providerId' => __('validation/SocialLoginRequest.providerId_attribute'),
        ];
    }
}
