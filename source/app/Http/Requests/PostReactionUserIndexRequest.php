<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostReactionUserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postId' => ['required', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'cursor' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'postId.required' => __('validation/PostReactionUserIndexRequest.postId_required'),
            'postId.integer' => __('validation/PostReactionUserIndexRequest.postId_integer'),
            'postId.min' => __('validation/PostReactionUserIndexRequest.postId_min'),
            'limit.integer' => __('validation/PostReactionUserIndexRequest.limit_integer'),
            'limit.min' => __('validation/PostReactionUserIndexRequest.limit_min'),
            'limit.max' => __('validation/PostReactionUserIndexRequest.limit_max'),
            'cursor.integer' => __('validation/PostReactionUserIndexRequest.cursor_integer'),
            'cursor.min' => __('validation/PostReactionUserIndexRequest.cursor_min'),
        ];
    }

    public function attributes(): array
    {
        return [
            'postId' => __('validation/PostReactionUserIndexRequest.postId_attribute'),
            'limit' => __('validation/PostReactionUserIndexRequest.limit_attribute'),
            'cursor' => __('validation/PostReactionUserIndexRequest.cursor_attribute'),
        ];
    }
}
