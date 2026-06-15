<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUserPostCommentRequest extends FormRequest
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
            'body' => ['nullable', 'required_without:media', 'string', 'max:2000'],
            'media' => ['nullable', 'file', 'max:10240'],
            'mediaType' => ['nullable', 'integer', 'in:1,3'],
        ];
    }

    public function messages(): array
    {
        return [
            'postId.required' => __('validation/PostUserPostCommentRequest.postId_required'),
            'postId.integer' => __('validation/PostUserPostCommentRequest.postId_integer'),
            'postId.min' => __('validation/PostUserPostCommentRequest.postId_min'),
            'parentId.integer' => __('validation/PostUserPostCommentRequest.parentId_integer'),
            'parentId.min' => __('validation/PostUserPostCommentRequest.parentId_min'),
            'body.required_without' => __('validation/PostUserPostCommentRequest.body_required_without'),
            'body.string' => __('validation/PostUserPostCommentRequest.body_string'),
            'body.max' => __('validation/PostUserPostCommentRequest.body_max'),
            'media.file' => __('validation/PostUserPostCommentRequest.media_file'),
            'media.max' => __('validation/PostUserPostCommentRequest.media_max'),
            'mediaType.integer' => __('validation/PostUserPostCommentRequest.mediaType_integer'),
            'mediaType.in' => __('validation/PostUserPostCommentRequest.mediaType_in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'postId' => __('validation/PostUserPostCommentRequest.postId_attribute'),
            'parentId' => __('validation/PostUserPostCommentRequest.parentId_attribute'),
            'body' => __('validation/PostUserPostCommentRequest.body_attribute'),
            'media' => __('validation/PostUserPostCommentRequest.media_attribute'),
            'mediaType' => __('validation/PostUserPostCommentRequest.mediaType_attribute'),
        ];
    }
}
