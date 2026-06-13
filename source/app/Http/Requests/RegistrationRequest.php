<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'password' => ['required', 'string'],
            'passwordConfirm' => ['required', 'string'],
            'credentialKeyId' => ['nullable', 'string'],
            'challengeId' => ['nullable', 'string'],
            'encryptedPassword' => ['nullable', 'string'],
            'encryptedPasswordConfirm' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => __('validation/RegistrationRequest.firstName_required'),
            'firstName.string' => __('validation/RegistrationRequest.firstName_string'),
            'lastName.string' => __('validation/RegistrationRequest.lastName_string'),
            'email.required' => __('validation/RegistrationRequest.email_required'),
            'email.string' => __('validation/RegistrationRequest.email_string'),
            'email.email' => __('validation/RegistrationRequest.email_email'),
            'password.required' => __('validation/RegistrationRequest.password_required'),
            'password.string' => __('validation/RegistrationRequest.password_string'),
            'passwordConfirm.required' => __('validation/RegistrationRequest.passwordConfirm_required'),
            'passwordConfirm.string' => __('validation/RegistrationRequest.passwordConfirm_string'),
            'credentialKeyId.string' => 'The credential key must be a string.',
            'challengeId.string' => 'The credential key must be a string.',
            'encryptedPassword.string' => __('validation/RegistrationRequest.password_string'),
            'encryptedPasswordConfirm.string' => __('validation/RegistrationRequest.passwordConfirm_string'),
        ];
    }

    public function attributes(): array
    {
        return [
            'firstName' => __('validation/RegistrationRequest.firstName_attribute'),
            'lastName' => __('validation/RegistrationRequest.lastName_attribute'),
            'email' => __('validation/RegistrationRequest.email_attribute'),
            'password' => __('validation/RegistrationRequest.password_attribute'),
            'passwordConfirm' => __('validation/RegistrationRequest.passwordConfirm_attribute'),
            'credentialKeyId' => 'credential key',
            'challengeId' => 'credential key',
            'encryptedPassword' => __('validation/RegistrationRequest.password_attribute'),
            'encryptedPasswordConfirm' => __('validation/RegistrationRequest.passwordConfirm_attribute'),
        ];
    }
}
