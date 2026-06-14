<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiInvalidArgumentException;
use App\Exceptions\ParameterException;
use App\Http\Requests\CommentIndexRequest;
use App\Http\Requests\CommentReactionUserIndexRequest;
use App\Http\Requests\PostReactionUserIndexRequest;
use App\Http\Requests\PostUserPostCommentReactionRequest;
use App\Http\Requests\PostUserPostCommentRequest;
use App\Http\Requests\PostUserPostReactionRequest;
use App\Services\PostApiService;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function __construct(private PostApiService $service)
    {
        parent::__construct();
    }

    public function postUserPost(Request $request)
    {
        $title = $request->input('title');
        if ($title !== null && $title !== '' && ! is_string($title)) {
            throw new ApiInvalidArgumentException('The value of title must be a string');
        }

        $body = $request->input('body');
        if ($body === null) {
            throw new ParameterException;
        }
        if ($body !== null && $body !== '' && ! is_string($body)) {
            throw new ApiInvalidArgumentException('The value of body must be a string');
        }

        $visibility = $request->input('visibility');
        $visibility = $visibility !== null ? (int) $request->input('visibility') : null;
        if ($visibility !== null && ! is_int($visibility)) {
            throw new ApiInvalidArgumentException('The value of visibility must be an integer');
        }

        $mediaUrl = $request->input('mediaUrl');
        if ($mediaUrl !== null && $mediaUrl !== '' && ! is_string($mediaUrl)) {
            throw new ApiInvalidArgumentException('The value of mediaUrl must be a string');
        }

        $thumbnailUrl = $request->input('thumbnailUrl');
        if ($thumbnailUrl !== null && $thumbnailUrl !== '' && ! is_string($thumbnailUrl)) {
            throw new ApiInvalidArgumentException('The value of thumbnailUrl must be a string');
        }

        $mediaType = $request->input('mediaType');
        $mediaType = $mediaType !== null ? (int) $request->input('mediaType') : null;
        if ($mediaType !== null && ! is_int($mediaType)) {
            throw new ApiInvalidArgumentException('The value of mediaType must be an integer');
        }

        return $this->jsonResponse(
            $request,
            $this->service->postUserPost(
                $title,
                $body,
                $visibility,
                $request->file('media'),
                $request->file('thumbnail'),
                $mediaUrl,
                $thumbnailUrl,
                $mediaType,
                $request
            ),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function postUserPostReaction(PostUserPostReactionRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->postUserPostReaction($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function getUserPostReactionUser(PostReactionUserIndexRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->getUserPostReactionUser($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function getUserPostComment(CommentIndexRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->getUserPostComment($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function postUserPostComment(PostUserPostCommentRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->postUserPostComment($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function postUserPostCommentReaction(PostUserPostCommentReactionRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->postUserPostCommentReaction($request),
            $this->getDefaultCacheTime(), 200
        );
    }

    public function getUserPostCommentReactionUser(CommentReactionUserIndexRequest $request)
    {
        return $this->jsonResponse(
            $request,
            $this->service->getUserPostCommentReactionUser($request),
            $this->getDefaultCacheTime(), 200
        );
    }
}
