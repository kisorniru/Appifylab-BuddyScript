<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'credentialKeyId' => ['nullable', 'string'],
            'challengeId' => ['nullable', 'string'],
            'encryptedPassword' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation/LoginRequest.email_required'),
            'email.string' => __('validation/LoginRequest.email_string'),
            'email.email' => __('validation/LoginRequest.email_email'),
            'password.required' => __('validation/LoginRequest.password_required'),
            'password.string' => __('validation/LoginRequest.password_string'),
            'credentialKeyId.string' => 'The credential key must be a string.',
            'challengeId.string' => 'The credential key must be a string.',
            'encryptedPassword.string' => __('validation/LoginRequest.password_string'),
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => __('validation/LoginRequest.email_attribute'),
            'password' => __('validation/LoginRequest.password_attribute'),
            'credentialKeyId' => 'credential key',
            'challengeId' => 'credential key',
            'encryptedPassword' => __('validation/LoginRequest.password_attribute'),
        ];
    }
}
