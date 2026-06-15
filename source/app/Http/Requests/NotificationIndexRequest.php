<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'cursor' => ['nullable', 'integer', 'min:1'],
            'unreadOnly' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'limit.integer' => __('validation/NotificationIndexRequest.limit_integer'),
            'limit.min' => __('validation/NotificationIndexRequest.limit_min'),
            'limit.max' => __('validation/NotificationIndexRequest.limit_max'),
            'cursor.integer' => __('validation/NotificationIndexRequest.cursor_integer'),
            'cursor.min' => __('validation/NotificationIndexRequest.cursor_min'),
            'unreadOnly.boolean' => 'The unread only field must be true or false.',
        ];
    }

    public function attributes(): array
    {
        return [
            'limit' => __('validation/NotificationIndexRequest.limit_attribute'),
            'cursor' => __('validation/NotificationIndexRequest.cursor_attribute'),
            'unreadOnly' => 'unread only',
        ];
    }
}
