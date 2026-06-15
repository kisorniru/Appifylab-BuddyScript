<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentReactionUserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commentId' => ['required', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'cursor' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'commentId.required' => __('validation/CommentReactionUserIndexRequest.commentId_required'),
            'commentId.integer' => __('validation/CommentReactionUserIndexRequest.commentId_integer'),
            'commentId.min' => __('validation/CommentReactionUserIndexRequest.commentId_min'),
            'limit.integer' => __('validation/CommentReactionUserIndexRequest.limit_integer'),
            'limit.min' => __('validation/CommentReactionUserIndexRequest.limit_min'),
            'limit.max' => __('validation/CommentReactionUserIndexRequest.limit_max'),
            'cursor.integer' => __('validation/CommentReactionUserIndexRequest.cursor_integer'),
            'cursor.min' => __('validation/CommentReactionUserIndexRequest.cursor_min'),
        ];
    }

    public function attributes(): array
    {
        return [
            'commentId' => __('validation/CommentReactionUserIndexRequest.commentId_attribute'),
            'limit' => __('validation/CommentReactionUserIndexRequest.limit_attribute'),
            'cursor' => __('validation/CommentReactionUserIndexRequest.cursor_attribute'),
        ];
    }
}
