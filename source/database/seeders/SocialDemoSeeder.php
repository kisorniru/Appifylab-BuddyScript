<?php

namespace Database\Seeders;

use App\Constants\MediaType;
use App\Constants\PostType;
use App\Constants\PostVisibility;
use App\Constants\ReactableType;
use App\Constants\ReactionType;
use App\Models\Comment;
use App\Models\CommentMedia;
use App\Models\CommentReactionStat;
use App\Models\FeedItem;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\PostMedia;
use App\Models\PostReactionStat;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SocialDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $users = User::query()
                ->where('is_admin', false)
                ->limit(30)
                ->get();

            if ($users->count() < 10) {
                $users = User::factory()->count(20)->create();
            }

            $users = User::query()->where('is_admin', false)->get();

            foreach ($users as $author) {
                $postCount = fake()->numberBetween(2, 5);

                for ($i = 0; $i < $postCount; $i++) {
                    $hasMedia = fake()->boolean(45);
                    $body = fake()->paragraphs(fake()->numberBetween(1, 3), true);

                    $post = Post::create([
                        'user_id' => $author->id,
                        'type' => $hasMedia ? PostType::MEDIA : PostType::TEXT,
                        'visibility' => fake()->boolean(85) ? PostVisibility::PUBLIC : PostVisibility::PRIVATE,
                    ]);

                    PostContent::create([
                        'post_id' => $post->id,
                        'body' => $body,
                        'excerpt' => Str::limit($body, 120),
                        'metadata' => null,
                    ]);

                    $mediaPreviewUrl = null;

                    if ($hasMedia) {
                        $seed = fake()->uuid();
                        $mediaPreviewUrl = "https://picsum.photos/seed/{$seed}/600/400";

                        PostMedia::create([
                            'post_id' => $post->id,
                            'media_type' => MediaType::IMAGE,
                            'file_url' => $mediaPreviewUrl,
                            'thumbnail_url' => "https://picsum.photos/seed/{$seed}/300/200",
                            'mime_type' => 'image/jpeg',
                            'size' => fake()->numberBetween(80_000, 2_000_000),
                            'width' => 600,
                            'height' => 400,
                            'duration' => null,
                            'metadata' => null,
                        ]);
                    }

                    PostReactionStat::create(['post_id' => $post->id]);

                    $rootComments = collect();
                    $commentUsers = $users->where('id', '!=', $author->id)->random(min($users->count() - 1, fake()->numberBetween(1, 5)));

                    foreach ($commentUsers as $commentUser) {
                        $comment = Comment::create([
                            'post_id' => $post->id,
                            'user_id' => $commentUser->id,
                            'parent_id' => null,
                            'body' => fake()->sentence(fake()->numberBetween(8, 18)),
                        ]);

                        CommentReactionStat::create(['comment_id' => $comment->id]);
                        $this->maybeCreateCommentMedia($comment);
                        $rootComments->push($comment);

                        $replyCount = fake()->numberBetween(0, 2);
                        for ($replyIndex = 0; $replyIndex < $replyCount; $replyIndex++) {
                            $replyUser = $users->random();
                            $reply = Comment::create([
                                'post_id' => $post->id,
                                'user_id' => $replyUser->id,
                                'parent_id' => $comment->id,
                                'body' => fake()->sentence(fake()->numberBetween(5, 14)),
                            ]);

                            CommentReactionStat::create(['comment_id' => $reply->id]);
                            $this->maybeCreateCommentMedia($reply);
                            $comment->increment('replies_count');
                        }
                    }

                    $post->update([
                        'comments_count' => Comment::where('post_id', $post->id)->count(),
                    ]);

                    $this->seedReactionsForPost($post, $users);
                    $this->seedReactionsForComments($post, $users);
                    $this->syncPostCounters($post);
                    $this->syncCommentCounters($post);

                    $this->createFeedItems($post, $users, $body, $mediaPreviewUrl);
                    $this->createNotifications($post, $users);
                }
            }
        });
    }

    private function seedReactionsForPost(Post $post, $users): void
    {
        $reactingUsers = $users->random(min($users->count(), fake()->numberBetween(1, 8)));

        foreach ($reactingUsers as $user) {
            Reaction::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'reactable_type' => ReactableType::POST,
                    'reactable_id' => $post->id,
                ],
                [
                    'reaction_type' => fake()->randomElement([ReactionType::LIKE, ReactionType::LOVE]),
                ]
            );
        }
    }

    private function maybeCreateCommentMedia(Comment $comment): void
    {
        if (! fake()->boolean(18)) {
            return;
        }

        if (fake()->boolean(65)) {
            $seed = fake()->uuid();

            CommentMedia::create([
                'comment_id' => $comment->id,
                'media_type' => MediaType::IMAGE,
                'file_url' => "https://picsum.photos/seed/{$seed}/480/360",
                'thumbnail_url' => "https://picsum.photos/seed/{$seed}/240/160",
                'mime_type' => 'image/jpeg',
                'size' => fake()->numberBetween(50_000, 1_000_000),
                'width' => 480,
                'height' => 360,
                'duration' => null,
                'metadata' => null,
            ]);

            return;
        }

        CommentMedia::create([
            'comment_id' => $comment->id,
            'media_type' => MediaType::AUDIO,
            'file_url' => 'https://example.com/audio/comment-'.fake()->uuid().'.webm',
            'thumbnail_url' => null,
            'mime_type' => 'audio/webm',
            'size' => fake()->numberBetween(20_000, 600_000),
            'width' => null,
            'height' => null,
            'duration' => fake()->numberBetween(3, 90),
            'metadata' => null,
        ]);
    }

    private function seedReactionsForComments(Post $post, $users): void
    {
        $comments = Comment::where('post_id', $post->id)->get();

        foreach ($comments as $comment) {
            $reactingUsers = $users->random(min($users->count(), fake()->numberBetween(0, 4)));

            foreach ($reactingUsers as $user) {
                Reaction::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'reactable_type' => ReactableType::COMMENT,
                        'reactable_id' => $comment->id,
                    ],
                    [
                        'reaction_type' => fake()->randomElement([ReactionType::LIKE, ReactionType::LOVE]),
                    ]
                );
            }
        }
    }

    private function syncPostCounters(Post $post): void
    {
        $likeCount = Reaction::forPost($post->id)->where('reaction_type', ReactionType::LIKE)->count();
        $loveCount = Reaction::forPost($post->id)->where('reaction_type', ReactionType::LOVE)->count();

        PostReactionStat::updateOrCreate(
            ['post_id' => $post->id],
            [
                'like_count' => $likeCount,
                'love_count' => $loveCount,
                'haha_count' => 0,
                'sad_count' => 0,
                'angry_count' => 0,
                'care_count' => 0,
            ]
        );

        $post->update([
            'reactions_count' => $likeCount + $loveCount,
            'comments_count' => Comment::where('post_id', $post->id)->count(),
        ]);
    }

    private function syncCommentCounters(Post $post): void
    {
        $comments = Comment::where('post_id', $post->id)->get();

        foreach ($comments as $comment) {
            $likeCount = Reaction::forComment($comment->id)->where('reaction_type', ReactionType::LIKE)->count();
            $loveCount = Reaction::forComment($comment->id)->where('reaction_type', ReactionType::LOVE)->count();

            CommentReactionStat::updateOrCreate(
                ['comment_id' => $comment->id],
                [
                    'like_count' => $likeCount,
                    'love_count' => $loveCount,
                    'haha_count' => 0,
                    'sad_count' => 0,
                    'angry_count' => 0,
                    'care_count' => 0,
                ]
            );

            $comment->update([
                'reactions_count' => $likeCount + $loveCount,
            ]);
        }
    }

    private function createFeedItems(Post $post, $users, string $body, ?string $mediaPreviewUrl): void
    {
        $author = $post->user;

        $viewers = $post->visibility === PostVisibility::PRIVATE
            ? collect([$author])
            : $users;

        foreach ($viewers as $viewer) {
            FeedItem::updateOrCreate(
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
                    'text_preview' => Str::limit($body, 160),
                    'media_preview_url' => $mediaPreviewUrl,
                    'reaction_count' => $post->reactions_count,
                    'comment_count' => $post->comments_count,
                    'share_count' => (int) ($post->shares_count ?? 0),
                    'viewer_reaction' => Reaction::forPost($post->id)
                        ->where('user_id', $viewer->id)
                        ->value('reaction_type'),
                ]
            );
        }
    }

    private function createNotifications(Post $post, $users): void
    {
        $receivers = $users
            ->where('id', '!=', $post->user_id)
            ->random(min(max($users->count() - 1, 0), fake()->numberBetween(1, 5)));

        foreach ($receivers as $receiver) {
            Notification::create([
                'sender_id' => $post->user_id,
                'receiver_id' => $receiver->id,
                'type' => 'post_created',
                'notifiable_type' => 'post',
                'notifiable_id' => $post->id,
                'data' => [
                    'message' => $post->user->name.' shared a new post.',
                    'post_id' => $post->id,
                ],
                'read_at' => fake()->boolean(30) ? now()->subMinutes(fake()->numberBetween(5, 5000)) : null,
            ]);
        }
    }
}
