<?php

namespace Database\Factories;

use App\Constants\ContentStatus;
use App\Models\Comment;
use App\Models\CommentMedia;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'body' => $this->faker->sentence($this->faker->numberBetween(6, 16)),
            'status' => ContentStatus::ACTIVE,
            'reactions_count' => 0,
            'replies_count' => 0,
        ];
    }

    public function replyTo(Comment $comment): self
    {
        return $this->state(fn () => [
            'post_id' => $comment->post_id,
            'parent_id' => $comment->id,
        ]);
    }

    public function withImageMedia(): self
    {
        return $this->afterCreating(function (Comment $comment) {
            CommentMedia::factory()->image()->create([
                'comment_id' => $comment->id,
            ]);
        });
    }

    public function withVoiceMedia(): self
    {
        return $this->afterCreating(function (Comment $comment) {
            CommentMedia::factory()->voice()->create([
                'comment_id' => $comment->id,
            ]);
        });
    }
}
