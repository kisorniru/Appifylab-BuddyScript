<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postId' => ['required', 'integer', 'min:1'],
            'parentId' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'cursor' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'postId.required' => __('validation/CommentIndexRequest.postId_required'),
            'postId.integer' => __('validation/CommentIndexRequest.postId_integer'),
            'postId.min' => __('validation/CommentIndexRequest.postId_min'),
            'parentId.integer' => __('validation/CommentIndexRequest.parentId_integer'),
            'parentId.min' => __('validation/CommentIndexRequest.parentId_min'),
            'limit.integer' => __('validation/CommentIndexRequest.limit_integer'),
            'limit.min' => __('validation/CommentIndexRequest.limit_min'),
            'limit.max' => __('validation/CommentIndexRequest.limit_max'),
            'cursor.integer' => __('validation/CommentIndexRequest.cursor_integer'),
            'cursor.min' => __('validation/CommentIndexRequest.cursor_min'),
        ];
    }

    public function attributes(): array
    {
        return [
            'postId' => __('validation/CommentIndexRequest.postId_attribute'),
            'parentId' => __('validation/CommentIndexRequest.parentId_attribute'),
            'limit' => __('validation/CommentIndexRequest.limit_attribute'),
            'cursor' => __('validation/CommentIndexRequest.cursor_attribute'),
        ];
    }
}
