<?php

namespace App\Services;

use App\Constants\MediaType;
use App\Constants\PostVisibility;
use App\Constants\ReactionType;
use App\Exceptions\ApiInvalidArgumentException;
use App\Exceptions\ParameterException;
use App\Http\Requests\CommentIndexRequest;
use App\Http\Requests\CommentReactionUserIndexRequest;
use App\Http\Requests\PostUserPostCommentReactionRequest;
use App\Http\Requests\PostUserPostCommentRequest;
use App\Http\Requests\PostUserPostReactionRequest;
use App\Http\Requests\PostReactionUserIndexRequest;
use App\Http\Responses\CommentItemResponse;
use App\Http\Responses\CommentMediaItemResponse;
use App\Http\Responses\CommentReactionResponse;
use App\Http\Responses\CommentResponse;
use App\Http\Responses\CommentResponseBody;
use App\Http\Responses\FeedAuthorResponse;
use App\Http\Responses\FeedItemResponse;
use App\Http\Responses\FeedMetaResponse;
use App\Http\Responses\PostCommentResponse;
use App\Http\Responses\PostReactionResponse;
use App\Http\Responses\PostReactionUserItemResponse;
use App\Http\Responses\PostReactionUserResponse;
use App\Http\Responses\PostReactionUserResponseBody;
use App\Http\Responses\PostResponse;
use App\Models\Comment;
use App\Models\CommentMedia;
use App\Models\FeedItem;
use App\Models\Reaction;
use App\Repositories\Contracts\PostApiRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostApiService
{
    public function __construct(private PostApiRepositoryInterface $postApiRepository) {}

    public function postUserPost(
        ?string $title,
        string $body,
        ?int $visibility,
        ?UploadedFile $media,
        ?UploadedFile $thumbnail,
        ?string $mediaUrl,
        ?string $thumbnailUrl,
        ?int $mediaType,
        Request $request
    ): PostResponse {
        DB::beginTransaction();

        try {
            if ($visibility !== null && ! in_array($visibility, [PostVisibility::PUBLIC, PostVisibility::PRIVATE], true)) {
                throw new ApiInvalidArgumentException('The value of visibility is invalid');
            }

            if ($mediaType !== null && ! in_array($mediaType, [MediaType::IMAGE, MediaType::VIDEO], true)) {
                throw new ApiInvalidArgumentException('The value of mediaType is invalid');
            }

            $post = $this->postApiRepository->createUserPost(
                $title,
                $body,
                $visibility,
                $media,
                $thumbnail,
                $mediaUrl,
                $thumbnailUrl,
                $mediaType,
                $request
            );
            $postMedia = $post->media->first();
            $feedItem = $this->postApiRepository->createFeedItemsForPost(
                $post,
                Str::limit($body, 160),
                $postMedia?->thumbnail_url ?: $postMedia?->file_url
            );

            $response = new PostResponse;
            $response->code = 200;
            $response->isSuccess = true;
            $response->message = 'Post created successfully';
            $response->responseBody = $this->toFeedItemResponse($feedItem);

            DB::commit();

            return $response;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function postUserPostReaction(PostUserPostReactionRequest $request): PostReactionResponse
    {
        DB::beginTransaction();

        try {
            $viewer = $request->user();
            $post = $this->postApiRepository->findVisiblePostForUser($viewer, (int) $request->input('postId'));
            if (! $post) {
                throw new ParameterException;
            }

            $feedItem = $this->postApiRepository->togglePostReaction(
                $post,
                $viewer,
                (int) ($request->input('reactionType') ?: ReactionType::LIKE)
            );

            $response = new PostReactionResponse;
            $response->code = 200;
            $response->isSuccess = true;
            $response->message = 'Post reaction updated successfully';
            $response->responseBody = $this->toFeedItemResponse($feedItem);

            DB::commit();

            return $response;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function getUserPostReactionUser(PostReactionUserIndexRequest $request): PostReactionUserResponse
    {
        $viewer = $request->user();
        $post = $this->postApiRepository->findVisiblePostForUser($viewer, (int) $request->input('postId'));
        if (! $post) {
            throw new ParameterException;
        }

        $limit = (int) ($request->input('limit') ?: 10);
        $cursor = $request->filled('cursor') ? (int) $request->input('cursor') : null;
        $reactions = $this->postApiRepository->getPostReactionUsers($post, $limit, $cursor);
        $hasMore = $reactions->count() > $limit;
        $visibleReactions = $reactions->take($limit)->values();

        $meta = new FeedMetaResponse;
        $meta->next_cursor = $hasMore ? $visibleReactions->last()?->id : null;
        $meta->has_more = $hasMore;

        $body = new PostReactionUserResponseBody;
        $body->items = $visibleReactions
            ->map(fn (Reaction $reaction) => $this->toPostReactionUserItemResponse($reaction))
            ->all();
        $body->meta = $meta;

        $response = new PostReactionUserResponse;
        $response->code = 200;
        $response->isSuccess = true;
        $response->message = 'Post reaction users fetched successfully';
        $response->responseBody = $body;

        return $response;
    }

    public function getUserPostComment(CommentIndexRequest $request): CommentResponse
    {
        $viewer = $request->user();
        $post = $this->postApiRepository->findVisiblePostForUser($viewer, (int) $request->input('postId'));
        if (! $post) {
            throw new ParameterException;
        }

        $limit = (int) ($request->input('limit') ?: 10);
        $cursor = $request->filled('cursor') ? (int) $request->input('cursor') : null;
        $parentId = $request->filled('parentId') ? (int) $request->input('parentId') : null;
        $comments = $this->postApiRepository->getPostComments($post, $limit, $cursor, $parentId);
        $hasMore = $comments->count() > $limit;
        $visibleComments = $comments->take($limit)->values();

        $meta = new FeedMetaResponse;
        $meta->next_cursor = $hasMore ? $visibleComments->last()?->id : null;
        $meta->has_more = $hasMore;

        $body = new CommentResponseBody;
        $body->items = $visibleComments
            ->map(fn (Comment $comment) => $this->toCommentItemResponse($comment, $viewer))
            ->all();
        $body->meta = $meta;

        $response = new CommentResponse;
        $response->code = 200;
        $response->isSuccess = true;
        $response->message = 'Comments fetched successfully';
        $response->responseBody = $body;

        return $response;
    }

    public function postUserPostComment(PostUserPostCommentRequest $request): PostCommentResponse
    {
        DB::beginTransaction();

        try {
            $viewer = $request->user();
            $post = $this->postApiRepository->findVisiblePostForUser($viewer, (int) $request->input('postId'));
            $body = trim((string) $request->input('body'));
            $media = $request->file('media');

            if (! $post || ($body === '' && ! $media)) {
                throw new ParameterException;
            }

            $comment = $this->postApiRepository->createPostComment(
                $post,
                $viewer,
                $body,
                $request->filled('parentId') ? (int) $request->input('parentId') : null,
                $media,
                $request->filled('mediaType') ? (int) $request->input('mediaType') : null
            );

            $response = new PostCommentResponse;
            $response->code = 200;
            $response->isSuccess = true;
            $response->message = 'Comment created successfully';
            $response->responseBody = $this->toCommentItemResponse($comment, $viewer);

            DB::commit();

            return $response;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function postUserPostCommentReaction(PostUserPostCommentReactionRequest $request): CommentReactionResponse
    {
        DB::beginTransaction();

        try {
            $viewer = $request->user();
            $comment = $this->postApiRepository->findVisibleCommentForUser($viewer, (int) $request->input('commentId'));
            if (! $comment) {
                throw new ParameterException;
            }

            $comment = $this->postApiRepository->toggleCommentReaction(
                $comment,
                $viewer,
                (int) ($request->input('reactionType') ?: ReactionType::LIKE)
            );

            $response = new CommentReactionResponse;
            $response->code = 200;
            $response->isSuccess = true;
            $response->message = 'Comment reaction updated successfully';
            $response->responseBody = $this->toCommentItemResponse($comment, $viewer);

            DB::commit();

            return $response;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function getUserPostCommentReactionUser(CommentReactionUserIndexRequest $request): PostReactionUserResponse
    {
        $viewer = $request->user();
        $comment = $this->postApiRepository->findVisibleCommentForUser($viewer, (int) $request->input('commentId'));
        if (! $comment) {
            throw new ParameterException;
        }

        $limit = (int) ($request->input('limit') ?: 10);
        $cursor = $request->filled('cursor') ? (int) $request->input('cursor') : null;
        $reactions = $this->postApiRepository->getCommentReactionUsers($comment, $limit, $cursor);
        $hasMore = $reactions->count() > $limit;
        $visibleReactions = $reactions->take($limit)->values();

        $meta = new FeedMetaResponse;
        $meta->next_cursor = $hasMore ? $visibleReactions->last()?->id : null;
        $meta->has_more = $hasMore;

        $body = new PostReactionUserResponseBody;
        $body->items = $visibleReactions
            ->map(fn (Reaction $reaction) => $this->toPostReactionUserItemResponse($reaction))
            ->all();
        $body->meta = $meta;

        $response = new PostReactionUserResponse;
        $response->code = 200;
        $response->isSuccess = true;
        $response->message = 'Comment reaction users fetched successfully';
        $response->responseBody = $body;

        return $response;
    }

    private function toFeedItemResponse(FeedItem $feedItem): FeedItemResponse
    {
        $item = new FeedItemResponse;
        $item->id = $feedItem->id;
        $item->postId = $feedItem->post_id;
        $item->author = $this->toAuthorResponse($feedItem->author_id, $feedItem->author_name, $feedItem->author_avatar);
        $item->text = $feedItem->text_preview;
        $item->image = $feedItem->media_preview_url;
        $item->postType = $feedItem->post_type;
        $item->visibility = $feedItem->visibility;
        $item->reactionCount = $feedItem->reaction_count;
        $item->reactors = $this->postApiRepository
            ->getPostReactors($feedItem->post)
            ->map(fn ($user) => $this->toAuthorResponse($user->id, $user->name, $user->profile_picture))
            ->all();
        $item->commentCount = $feedItem->comment_count;
        $item->shareCount = $feedItem->share_count;
        $item->viewerReaction = $feedItem->viewer_reaction;
        $item->createdAt = $feedItem->created_at?->toISOString();

        return $item;
    }

    private function toCommentItemResponse(Comment $comment, $viewer = null): CommentItemResponse
    {
        $item = new CommentItemResponse;
        $item->id = $comment->id;
        $item->postId = $comment->post_id;
        $item->parentId = $comment->parent_id;
        $item->author = $this->toAuthorResponse($comment->user_id, $comment->user?->name, $comment->user?->profile_picture);
        $item->body = $comment->body;
        $item->media = $comment->media
            ->map(fn (CommentMedia $media) => $this->toCommentMediaItemResponse($media))
            ->all();
        $item->reactionCount = $comment->reactions_count;
        $item->reactors = $this->postApiRepository
            ->getCommentReactors($comment)
            ->map(fn ($user) => $this->toAuthorResponse($user->id, $user->name, $user->profile_picture))
            ->all();
        $item->replyCount = $comment->replies_count;
        $item->viewerReaction = $viewer ? $this->postApiRepository->getCommentViewerReaction($comment, $viewer) : null;
        $item->createdAt = $comment->created_at?->toISOString();

        return $item;
    }

    private function toPostReactionUserItemResponse(Reaction $reaction): PostReactionUserItemResponse
    {
        $item = new PostReactionUserItemResponse;
        $item->id = $reaction->id;
        $item->user = $this->toAuthorResponse($reaction->user_id, $reaction->user?->name, $reaction->user?->profile_picture);
        $item->reactionType = $reaction->reaction_type;
        $item->reactedAt = $reaction->created_at?->toISOString();

        return $item;
    }

    private function toCommentMediaItemResponse(CommentMedia $media): CommentMediaItemResponse
    {
        $item = new CommentMediaItemResponse;
        $item->id = $media->id;
        $item->mediaType = $media->media_type;
        $item->fileUrl = $media->file_url;
        $item->thumbnailUrl = $media->thumbnail_url;
        $item->mimeType = $media->mime_type;
        $item->duration = $media->duration;

        return $item;
    }

    private function toAuthorResponse(?int $id, ?string $name, ?string $avatar): FeedAuthorResponse
    {
        $author = new FeedAuthorResponse;
        $author->id = $id;
        $author->name = $name;
        $author->avatar = $avatar;

        return $author;
    }
}
