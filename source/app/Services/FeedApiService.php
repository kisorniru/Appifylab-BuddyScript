<?php

namespace App\Services;

use App\Http\Requests\FeedIndexRequest;
use App\Http\Responses\FeedAuthorResponse;
use App\Http\Responses\FeedItemResponse;
use App\Http\Responses\FeedMetaResponse;
use App\Http\Responses\FeedResponse;
use App\Http\Responses\FeedResponseBody;
use App\Models\FeedItem;
use App\Repositories\Contracts\FeedApiRepositoryInterface;

class FeedApiService
{
    public function __construct(private FeedApiRepositoryInterface $feedApiRepository) {}

    public function getUserFeed(FeedIndexRequest $request): FeedResponse
    {
        $viewer = $request->user();
        $limit = (int) ($request->input('limit') ?: 10);
        $cursor = $request->filled('cursor') ? (int) $request->input('cursor') : null;

        if (! $cursor) {
            $this->feedApiRepository->ensureFeedItemsForViewer($viewer);
        }

        $feedItems = $this->feedApiRepository->getUserFeed($viewer, $limit, $cursor);
        $hasMore = $feedItems->count() > $limit;
        $visibleItems = $feedItems->take($limit)->values();

        $meta = new FeedMetaResponse;
        $meta->next_cursor = $hasMore ? $visibleItems->last()?->id : null;
        $meta->has_more = $hasMore;

        $body = new FeedResponseBody;
        $body->items = $visibleItems
            ->map(fn (FeedItem $feedItem) => $this->toFeedItemResponse($feedItem))
            ->all();
        $body->meta = $meta;

        $response = new FeedResponse;
        $response->code = 200;
        $response->isSuccess = true;
        $response->message = 'Feed fetched successfully';
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
        $item->reactors = $this->feedApiRepository
            ->getPostReactors($feedItem->post)
            ->map(fn ($user) => $this->toAuthorResponse($user->id, $user->name, $user->profile_picture))
            ->all();
        $item->commentCount = $feedItem->comment_count;
        $item->shareCount = $feedItem->share_count;
        $item->viewerReaction = $feedItem->viewer_reaction;
        $item->createdAt = $feedItem->created_at?->toISOString();

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
