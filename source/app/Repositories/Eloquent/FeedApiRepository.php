<?php

namespace App\Repositories\Eloquent;

use App\Constants\PostVisibility;
use App\Models\FeedItem;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use App\Repositories\Contracts\FeedApiRepositoryInterface;
use Illuminate\Support\Collection;

class FeedApiRepository implements FeedApiRepositoryInterface
{
    public function ensureFeedItemsForViewer(User $viewer): void
    {
        $existingPostIds = FeedItem::query()
            ->where('viewer_id', $viewer->id)
            ->pluck('post_id');

        Post::query()
            ->with(['user', 'content', 'media'])
            ->active()
            ->visibleFor($viewer->id)
            ->whereNotIn('id', $existingPostIds)
            ->orderBy('created_at')
            ->chunkById(100, function ($posts) use ($viewer) {
                foreach ($posts as $post) {
                    $media = $post->media->first();

                    FeedItem::updateOrCreate(
                        [
                            'viewer_id' => $viewer->id,
                            'post_id' => $post->id,
                        ],
                        [
                            'author_id' => $post->user_id,
                            'author_name' => $post->user?->name ?: 'Unknown user',
                            'author_avatar' => $post->user?->profile_picture,
                            'post_type' => $post->type,
                            'visibility' => $post->visibility ?: PostVisibility::PUBLIC,
                            'text_preview' => $post->content?->excerpt ?: $post->content?->body,
                            'media_preview_url' => $media?->thumbnail_url ?: $media?->file_url,
                            'reaction_count' => $post->reactions_count,
                            'comment_count' => $post->comments_count,
                            'share_count' => $post->shares_count,
                            'viewer_reaction' => Reaction::forPost($post->id)
                                ->where('user_id', $viewer->id)
                                ->value('reaction_type'),
                        ]
                    );
                }
            });
    }

    public function getUserFeed(User $viewer, int $limit, ?int $cursor = null): Collection
    {
        return FeedItem::query()
            ->where('viewer_id', $viewer->id)
            ->when($cursor, fn ($query) => $query->where('id', '<', $cursor))
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();
    }

    public function getPostReactors(Post $post, int $limit = 4): Collection
    {
        return Reaction::forPost($post->id)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->pluck('user')
            ->filter()
            ->values();
    }
}
