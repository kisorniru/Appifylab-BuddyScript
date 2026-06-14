<?php

namespace App\Http\Requests;

use App\Constants\MediaType;
use App\Constants\PostVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostUserPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'visibility' => ['nullable', 'integer', Rule::in([PostVisibility::PUBLIC, PostVisibility::PRIVATE])],
            'media' => ['nullable', 'file', 'mimes:webp,jpg,jpeg,png,webm,mp4,avi,mov', 'max:10240'],
            'thumbnail' => ['nullable', 'image', 'mimes:webp,jpg,jpeg,png', 'max:5120'],
            'mediaUrl' => ['nullable', 'url', 'max:2048'],
            'thumbnailUrl' => ['nullable', 'url', 'max:2048'],
            'mediaType' => ['nullable', 'integer', Rule::in([MediaType::IMAGE, MediaType::VIDEO])],
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => __('validation/PostUserPostRequest.title_string'),
            'title.max' => __('validation/PostUserPostRequest.title_max'),
            'body.required' => __('validation/PostUserPostRequest.body_required'),
            'body.string' => __('validation/PostUserPostRequest.body_string'),
            'body.max' => __('validation/PostUserPostRequest.body_max'),
            'visibility.integer' => __('validation/PostUserPostRequest.visibility_integer'),
            'visibility.in' => __('validation/PostUserPostRequest.visibility_in'),
            'media.file' => __('validation/PostUserPostRequest.media_file'),
            'media.mimes' => __('validation/PostUserPostRequest.media_mimes'),
            'media.max' => __('validation/PostUserPostRequest.media_max'),
            'thumbnail.image' => __('validation/PostUserPostRequest.thumbnail_image'),
            'thumbnail.mimes' => __('validation/PostUserPostRequest.thumbnail_mimes'),
            'thumbnail.max' => __('validation/PostUserPostRequest.thumbnail_max'),
            'mediaUrl.url' => __('validation/PostUserPostRequest.mediaUrl_url'),
            'mediaUrl.max' => __('validation/PostUserPostRequest.mediaUrl_max'),
            'thumbnailUrl.url' => __('validation/PostUserPostRequest.thumbnailUrl_url'),
            'thumbnailUrl.max' => __('validation/PostUserPostRequest.thumbnailUrl_max'),
            'mediaType.integer' => __('validation/PostUserPostRequest.mediaType_integer'),
            'mediaType.in' => __('validation/PostUserPostRequest.mediaType_in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('validation/PostUserPostRequest.title_attribute'),
            'body' => __('validation/PostUserPostRequest.body_attribute'),
            'visibility' => __('validation/PostUserPostRequest.visibility_attribute'),
            'media' => __('validation/PostUserPostRequest.media_attribute'),
            'thumbnail' => __('validation/PostUserPostRequest.thumbnail_attribute'),
            'mediaUrl' => __('validation/PostUserPostRequest.mediaUrl_attribute'),
            'thumbnailUrl' => __('validation/PostUserPostRequest.thumbnailUrl_attribute'),
            'mediaType' => __('validation/PostUserPostRequest.mediaType_attribute'),
        ];
    }
}
