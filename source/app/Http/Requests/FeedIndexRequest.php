<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedIndexRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'limit.integer' => __('validation/FeedIndexRequest.limit_integer'),
            'limit.min' => __('validation/FeedIndexRequest.limit_min'),
            'limit.max' => __('validation/FeedIndexRequest.limit_max'),
            'cursor.integer' => __('validation/FeedIndexRequest.cursor_integer'),
            'cursor.min' => __('validation/FeedIndexRequest.cursor_min'),
        ];
    }

    public function attributes(): array
    {
        return [
            'limit' => __('validation/FeedIndexRequest.limit_attribute'),
            'cursor' => __('validation/FeedIndexRequest.cursor_attribute'),
        ];
    }
}
