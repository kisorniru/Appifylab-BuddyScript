<?php

namespace App\Repositories\Contracts;

use App\Models\Comment;
use App\Models\FeedItem;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface PostApiRepositoryInterface
{
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
    ): Post;

    public function createFeedItemsForPost(Post $post, string $textPreview, ?string $mediaPreviewUrl = null): FeedItem;

    public function findVisiblePostForUser(User $viewer, int $postId): ?Post;

    public function getFeedItemForViewer(Post $post, User $viewer): ?FeedItem;

    public function getPostReactors(Post $post, int $limit = 4): Collection;

    public function getPostReactionUsers(Post $post, int $limit, ?int $cursor = null): Collection;

    public function togglePostReaction(Post $post, User $viewer, int $reactionType): FeedItem;

    public function getPostComments(Post $post, int $limit, ?int $cursor = null, ?int $parentId = null): Collection;

    public function createPostComment(
        Post $post,
        User $author,
        string $body,
        ?int $parentId = null,
        ?UploadedFile $media = null,
        ?int $mediaType = null
    ): Comment;

    public function findVisibleCommentForUser(User $viewer, int $commentId): ?Comment;

    public function getCommentViewerReaction(Comment $comment, User $viewer): ?int;

    public function getCommentReactors(Comment $comment, int $limit = 4): Collection;

    public function getCommentReactionUsers(Comment $comment, int $limit, ?int $cursor = null): Collection;

    public function toggleCommentReaction(Comment $comment, User $viewer, int $reactionType): Comment;
}
