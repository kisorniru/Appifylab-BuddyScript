<?php

namespace App\Repositories\Eloquent;

use App\Constants\ContentStatus;
use App\Constants\MediaType;
use App\Constants\PostType;
use App\Constants\PostVisibility;
use App\Constants\ReactableType;
use App\Constants\ReactionType;
use App\Constants\StorageUrl;
use App\Exceptions\ApiInvalidArgumentException;
use App\Exceptions\ApiInvalidFileSizeException;
use App\Exceptions\ApiInvalidImageFileTypeException;
use App\Managers\StorageManager;
use App\Models\Comment;
use App\Models\CommentMedia;
use App\Models\CommentReactionStat;
use App\Models\FeedItem;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\PostMedia;
use App\Models\PostReactionStat;
use App\Models\Reaction;
use App\Models\User;
use App\Repositories\Contracts\NotificationApiRepositoryInterface;
use App\Repositories\Contracts\PostApiRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PostApiRepository implements PostApiRepositoryInterface
{
    public function __construct(private NotificationApiRepositoryInterface $notificationApiRepository) {}

    public function createUserPost(
        ?string $title,
        string $body,
        ?int $visibility,
        ?UploadedFile $media,
        ?UploadedFile $thumbnail,
        ?string $mediaUrl,
        ?string $thumbnailUrl,
        ?int $mediaType,
        Request $request
    ): Post {
        $author = $request->user();
        $storedMediaUrl = $mediaUrl;
        $storedThumbnailUrl = $thumbnailUrl;
        $storedMediaType = $mediaType ?: MediaType::IMAGE;

        if ($media) {
            if (! $media->isValid() || ! StorageManager::isSizeValid($media)) {
                throw new ApiInvalidFileSizeException;
            }

            $storedMediaType = $mediaType ?: ($this->isVideoUpload($media) ? MediaType::VIDEO : MediaType::IMAGE);

            if ($storedMediaType === MediaType::VIDEO) {
                if (! StorageManager::isVideoValid($media)) {
                    throw new ApiInvalidArgumentException('The value of media must be a valid video file');
                }

                $storedMediaUrl = StorageManager::saveVideoAsWebm(
                    $media,
                    StorageUrl::POST_MEDIA,
                    ['{userId}' => $author->id, '{fileName}' => $media->getClientOriginalName()]
                );
            } else {
                if (! StorageManager::isImageValid($media)) {
                    throw new ApiInvalidImageFileTypeException;
                }

                $storedMediaUrl = StorageManager::saveImageAsWebp(
                    $media,
                    StorageUrl::POST_MEDIA,
                    ['{userId}' => $author->id, '{fileName}' => $media->getClientOriginalName()],
                    [],
                    70
                );
            }
        }

        if ($thumbnail) {
            if (! $thumbnail->isValid() || ! StorageManager::isSizeValid($thumbnail)) {
                throw new ApiInvalidFileSizeException;
            }

            if (! StorageManager::isImageValid($thumbnail)) {
                throw new ApiInvalidImageFileTypeException;
            }

            $storedThumbnailUrl = StorageManager::saveImageAsWebp(
                $thumbnail,
                StorageUrl::POST_THUMBNAIL,
                ['{userId}' => $author->id, '{fileName}' => $thumbnail->getClientOriginalName()],
                ['width' => 600, 'height' => 400],
                70
            );
        }

        $post = Post::create([
            'user_id' => $author->id,
            'type' => $storedMediaUrl ? PostType::MEDIA : PostType::TEXT,
            'visibility' => $visibility ?: PostVisibility::PUBLIC,
            'status' => ContentStatus::ACTIVE,
            'comments_count' => 0,
            'reactions_count' => 0,
            'shares_count' => 0,
        ]);

        PostContent::create([
            'post_id' => $post->id,
            'title' => $title,
            'body' => $body,
            'excerpt' => Str::limit($body, 160),
            'metadata' => null,
        ]);

        if ($storedMediaUrl) {
            PostMedia::create([
                'post_id' => $post->id,
                'media_type' => $storedMediaType,
                'file_url' => $storedMediaUrl,
                'thumbnail_url' => $storedThumbnailUrl,
                'mime_type' => $storedMediaType === MediaType::VIDEO ? 'video/webm' : 'image/webp',
                'size' => null,
                'width' => null,
                'height' => null,
                'duration' => null,
                'metadata' => null,
            ]);
        }

        PostReactionStat::create(['post_id' => $post->id]);

        return $post->load(['content', 'media']);
    }

    public function createFeedItemsForPost(Post $post, string $textPreview, ?string $mediaPreviewUrl = null): FeedItem
    {
        $author = $post->user;
        $viewers = $post->visibility === PostVisibility::PRIVATE
            ? collect([$author])
            : User::query()->where('is_admin', false)->get();

        if (! $viewers->contains('id', $author->id)) {
            $viewers->push($author);
        }

        $createdAuthorFeedItem = null;

        foreach ($viewers as $viewer) {
            $feedItem = FeedItem::updateOrCreate(
                [
                    'viewer_id' => $viewer->id,
                    'post_id' => $post->id,
                ],
                [
                    'author_id' => $author->id,
                    'author_name' => $author->name,
                    'author_avatar' => $author->profile_picture,
                    'post_type' => $post->type,
                    'visibility' => $post->visibility,
                    'text_preview' => $textPreview,
                    'media_preview_url' => $mediaPreviewUrl,
                    'reaction_count' => 0,
                    'comment_count' => 0,
                    'share_count' => 0,
                    'viewer_reaction' => null,
                ]
            );

            if ($viewer->id === $author->id) {
                $createdAuthorFeedItem = $feedItem;
            }
        }

        return $createdAuthorFeedItem ?: FeedItem::where('post_id', $post->id)->first();
    }

    public function findVisiblePostForUser(User $viewer, int $postId): ?Post
    {
        return Post::query()
            ->active()
            ->visibleFor($viewer->id)
            ->where('id', $postId)
            ->first();
    }

    public function getFeedItemForViewer(Post $post, User $viewer): ?FeedItem
    {
        return FeedItem::query()
            ->where('post_id', $post->id)
            ->where('viewer_id', $viewer->id)
            ->first();
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

    public function getPostReactionUsers(Post $post, int $limit, ?int $cursor = null): Collection
    {
        return Reaction::forPost($post->id)
            ->with('user')
            ->when($cursor, fn ($query) => $query->where('id', '<', $cursor))
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();
    }

    public function togglePostReaction(Post $post, User $viewer, int $reactionType): FeedItem
    {
        $reaction = Reaction::forPost($post->id)
            ->where('user_id', $viewer->id)
            ->first();

        if ($reaction && (int) $reaction->reaction_type === $reactionType) {
            $this->decrementPostReaction($post, $reactionType);
            $reaction->delete();
            $viewerReaction = null;
        } elseif ($reaction) {
            $this->decrementPostReaction($post, (int) $reaction->reaction_type);
            $reaction->update(['reaction_type' => $reactionType]);
            $this->incrementPostReaction($post, $reactionType);
            $this->notifyPostReactionCreated($post, $viewer, $reactionType);
            $viewerReaction = $reactionType;
        } else {
            Reaction::create([
                'user_id' => $viewer->id,
                'reactable_type' => ReactableType::POST,
                'reactable_id' => $post->id,
                'reaction_type' => $reactionType,
            ]);

            $this->incrementPostReaction($post, $reactionType);
            $this->notifyPostReactionCreated($post, $viewer, $reactionType);
            $viewerReaction = $reactionType;
        }

        $post->refresh();
        FeedItem::where('post_id', $post->id)->update(['reaction_count' => $post->reactions_count]);
        FeedItem::where('post_id', $post->id)
            ->where('viewer_id', $viewer->id)
            ->update(['viewer_reaction' => $viewerReaction]);

        return $this->getFeedItemForViewer($post, $viewer)
            ?: FeedItem::where('post_id', $post->id)->firstOrFail();
    }

    public function getPostComments(Post $post, int $limit, ?int $cursor = null, ?int $parentId = null): Collection
    {
        return Comment::query()
            ->with(['user', 'media'])
            ->active()
            ->where('post_id', $post->id)
            ->when($parentId, fn ($query) => $query->where('parent_id', $parentId), fn ($query) => $query->whereNull('parent_id'))
            ->when($cursor, fn ($query) => $query->where('id', '<', $cursor))
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();
    }

    public function createPostComment(
        Post $post,
        User $author,
        string $body,
        ?int $parentId = null,
        ?UploadedFile $media = null,
        ?int $mediaType = null
    ): Comment {
        $parent = null;
        if ($parentId) {
            $parent = Comment::query()
                ->active()
                ->where('post_id', $post->id)
                ->whereNull('parent_id')
                ->where('id', $parentId)
                ->firstOrFail();
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'parent_id' => $parent?->id,
            'body' => $body,
            'status' => ContentStatus::ACTIVE,
            'reactions_count' => 0,
            'replies_count' => 0,
        ]);

        CommentReactionStat::create(['comment_id' => $comment->id]);

        if ($media) {
            $this->saveCommentMedia($comment, $author, $media, $mediaType);
        }

        if ($parent) {
            $parent->increment('replies_count');
            $this->notifyCommentReplyCreated($parent, $comment, $author);
        } else {
            $this->notifyPostCommentCreated($post, $comment, $author);
        }

        $post->increment('comments_count');
        FeedItem::where('post_id', $post->id)->increment('comment_count');

        return $comment->load(['user', 'media']);
    }

    public function findVisibleCommentForUser(User $viewer, int $commentId): ?Comment
    {
        return Comment::query()
            ->with(['user', 'media'])
            ->active()
            ->where('id', $commentId)
            ->whereHas('post', fn ($query) => $query->active()->visibleFor($viewer->id))
            ->first();
    }

    public function getCommentViewerReaction(Comment $comment, User $viewer): ?int
    {
        return Reaction::forComment($comment->id)
            ->where('user_id', $viewer->id)
            ->value('reaction_type');
    }

    public function getCommentReactors(Comment $comment, int $limit = 4): Collection
    {
        return Reaction::forComment($comment->id)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->pluck('user')
            ->filter()
            ->values();
    }

    public function getCommentReactionUsers(Comment $comment, int $limit, ?int $cursor = null): Collection
    {
        return Reaction::forComment($comment->id)
            ->with('user')
            ->when($cursor, fn ($query) => $query->where('id', '<', $cursor))
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();
    }

    public function toggleCommentReaction(Comment $comment, User $viewer, int $reactionType): Comment
    {
        $reaction = Reaction::forComment($comment->id)
            ->where('user_id', $viewer->id)
            ->first();

        if ($reaction && (int) $reaction->reaction_type === $reactionType) {
            $this->decrementCommentReaction($comment, $reactionType);
            $reaction->delete();

            return $comment->refresh()->load(['user', 'media']);
        }

        if ($reaction) {
            $this->decrementCommentReaction($comment, (int) $reaction->reaction_type);
            $reaction->update(['reaction_type' => $reactionType]);
            $this->incrementCommentReaction($comment, $reactionType);
            $this->notifyCommentReactionCreated($comment, $viewer, $reactionType);

            return $comment->refresh()->load(['user', 'media']);
        }

        Reaction::create([
            'user_id' => $viewer->id,
            'reactable_type' => ReactableType::COMMENT,
            'reactable_id' => $comment->id,
            'reaction_type' => $reactionType,
        ]);

        $this->incrementCommentReaction($comment, $reactionType);
        $this->notifyCommentReactionCreated($comment, $viewer, $reactionType);

        return $comment->refresh()->load(['user', 'media']);
    }

    private function saveCommentMedia(Comment $comment, User $author, UploadedFile $media, ?int $mediaType): void
    {
        if (! $media->isValid() || ! StorageManager::isSizeValid($media)) {
            throw new ApiInvalidFileSizeException;
        }

        $storedMediaType = $mediaType ?: ($this->isAudioUpload($media) ? MediaType::AUDIO : MediaType::IMAGE);
        $storedMediaUrl = null;
        $storedThumbnailUrl = null;
        $mimeType = null;

        if ($storedMediaType === MediaType::AUDIO) {
            if (! StorageManager::isAudioValid($media)) {
                throw new ApiInvalidArgumentException('The value of media must be a valid audio file');
            }

            $storedMediaUrl = StorageManager::uploadFile(
                $media,
                StorageUrl::COMMENT_MEDIA,
                ['{userId}' => $author->id, '{fileName}' => $media->getClientOriginalName()]
            );
            $mimeType = $media->getMimeType() ?: 'audio/webm';
        } else {
            if (! StorageManager::isImageValid($media)) {
                throw new ApiInvalidImageFileTypeException;
            }

            $storedMediaUrl = StorageManager::saveImageAsWebp(
                $media,
                StorageUrl::COMMENT_MEDIA,
                ['{userId}' => $author->id, '{fileName}' => $media->getClientOriginalName()],
                [],
                70
            );
            $storedThumbnailUrl = StorageManager::saveImageAsWebp(
                $media,
                StorageUrl::COMMENT_THUMBNAIL,
                ['{userId}' => $author->id, '{fileName}' => $media->getClientOriginalName()],
                ['width' => 240, 'height' => 160],
                70
            );
            $mimeType = 'image/webp';
        }

        CommentMedia::create([
            'comment_id' => $comment->id,
            'media_type' => $storedMediaType,
            'file_url' => $storedMediaUrl,
            'thumbnail_url' => $storedThumbnailUrl,
            'mime_type' => $mimeType,
            'size' => $media->getSize(),
            'width' => null,
            'height' => null,
            'duration' => null,
            'metadata' => null,
        ]);
    }

    private function incrementPostReaction(Post $post, int $reactionType): void
    {
        $column = $reactionType === ReactionType::LOVE ? 'love_count' : 'like_count';

        PostReactionStat::firstOrCreate(['post_id' => $post->id])->increment($column);
        $post->increment('reactions_count');
    }

    private function decrementPostReaction(Post $post, int $reactionType): void
    {
        $column = $reactionType === ReactionType::LOVE ? 'love_count' : 'like_count';
        $stat = PostReactionStat::firstOrCreate(['post_id' => $post->id]);

        if ((int) $stat->{$column} > 0) {
            $stat->decrement($column);
        }

        if ((int) $post->reactions_count > 0) {
            $post->decrement('reactions_count');
        }
    }

    private function incrementCommentReaction(Comment $comment, int $reactionType): void
    {
        $column = $reactionType === ReactionType::LOVE ? 'love_count' : 'like_count';

        CommentReactionStat::firstOrCreate(['comment_id' => $comment->id])->increment($column);
        $comment->increment('reactions_count');
    }

    private function decrementCommentReaction(Comment $comment, int $reactionType): void
    {
        $column = $reactionType === ReactionType::LOVE ? 'love_count' : 'like_count';
        $stat = CommentReactionStat::firstOrCreate(['comment_id' => $comment->id]);

        if ((int) $stat->{$column} > 0) {
            $stat->decrement($column);
        }

        if ((int) $comment->reactions_count > 0) {
            $comment->decrement('reactions_count');
        }
    }

    private function notifyPostCommentCreated(Post $post, Comment $comment, User $sender): void
    {
        $receiver = $post->user;
        if (! $receiver) {
            return;
        }

        $this->notificationApiRepository->createNotification(
            $sender,
            $receiver,
            'comment_created',
            'post',
            $post->id,
            [
                'message' => "{$sender->name} commented on your post.",
                'post_id' => $post->id,
                'comment_id' => $comment->id,
            ]
        );
    }

    private function notifyCommentReplyCreated(Comment $parent, Comment $reply, User $sender): void
    {
        $receiver = $parent->user;
        if (! $receiver) {
            return;
        }

        $this->notificationApiRepository->createNotification(
            $sender,
            $receiver,
            'reply_created',
            'comment',
            $parent->id,
            [
                'message' => "{$sender->name} replied to your comment.",
                'post_id' => $parent->post_id,
                'comment_id' => $parent->id,
                'reply_id' => $reply->id,
            ]
        );
    }

    private function notifyPostReactionCreated(Post $post, User $sender, int $reactionType): void
    {
        $receiver = $post->user;
        if (! $receiver) {
            return;
        }

        $reactionName = $reactionType === ReactionType::LOVE ? 'loved' : 'liked';

        $this->notificationApiRepository->createNotification(
            $sender,
            $receiver,
            'post_reacted',
            'post',
            $post->id,
            [
                'message' => "{$sender->name} {$reactionName} your post.",
                'post_id' => $post->id,
                'reaction_type' => $reactionType,
            ]
        );
    }

    private function notifyCommentReactionCreated(Comment $comment, User $sender, int $reactionType): void
    {
        $receiver = $comment->user;
        if (! $receiver) {
            return;
        }

        $reactionName = $reactionType === ReactionType::LOVE ? 'loved' : 'liked';

        $this->notificationApiRepository->createNotification(
            $sender,
            $receiver,
            'comment_reacted',
            'comment',
            $comment->id,
            [
                'message' => "{$sender->name} {$reactionName} your comment.",
                'post_id' => $comment->post_id,
                'comment_id' => $comment->id,
                'reaction_type' => $reactionType,
            ]
        );
    }

    private function isVideoUpload(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return str_starts_with((string) $file->getMimeType(), 'video/')
            || in_array($extension, ['webm', 'mp4', 'avi', 'mov'], true);
    }

    private function isAudioUpload(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return str_starts_with((string) $file->getMimeType(), 'audio/')
            || in_array($extension, ['webm', 'mp3', 'wav', 'ogg', 'm4a'], true);
    }
}
