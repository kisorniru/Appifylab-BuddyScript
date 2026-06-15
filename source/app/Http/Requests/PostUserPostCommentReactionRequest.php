<?php

namespace App\Http\Requests;

use App\Constants\ReactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostUserPostCommentReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commentId' => ['required', 'integer', 'min:1'],
            'reactionType' => ['nullable', 'integer', Rule::in([ReactionType::LIKE, ReactionType::LOVE])],
        ];
    }

    public function messages(): array
    {
        return [
            'commentId.required' => __('validation/PostUserPostCommentReactionRequest.commentId_required'),
            'commentId.integer' => __('validation/PostUserPostCommentReactionRequest.commentId_integer'),
            'commentId.min' => __('validation/PostUserPostCommentReactionRequest.commentId_min'),
            'reactionType.integer' => __('validation/PostUserPostCommentReactionRequest.reactionType_integer'),
            'reactionType.in' => __('validation/PostUserPostCommentReactionRequest.reactionType_in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'commentId' => __('validation/PostUserPostCommentReactionRequest.commentId_attribute'),
            'reactionType' => __('validation/PostUserPostCommentReactionRequest.reactionType_attribute'),
        ];
    }
}
