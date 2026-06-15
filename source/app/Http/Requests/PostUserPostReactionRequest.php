<?php

namespace App\Http\Requests;

use App\Constants\ReactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostUserPostReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postId' => ['required', 'integer', 'min:1'],
            'reactionType' => ['nullable', 'integer', Rule::in([ReactionType::LIKE, ReactionType::LOVE])],
        ];
    }

    public function messages(): array
    {
        return [
            'postId.required' => __('validation/PostUserPostReactionRequest.postId_required'),
            'postId.integer' => __('validation/PostUserPostReactionRequest.postId_integer'),
            'postId.min' => __('validation/PostUserPostReactionRequest.postId_min'),
            'reactionType.integer' => __('validation/PostUserPostReactionRequest.reactionType_integer'),
            'reactionType.in' => __('validation/PostUserPostReactionRequest.reactionType_in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'postId' => __('validation/PostUserPostReactionRequest.postId_attribute'),
            'reactionType' => __('validation/PostUserPostReactionRequest.reactionType_attribute'),
        ];
    }
}
