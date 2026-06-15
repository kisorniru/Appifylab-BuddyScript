<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentReactionStat;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentReactionStatFactory extends Factory
{
    protected $model = CommentReactionStat::class;

    public function definition(): array
    {
        return [
            'comment_id' => Comment::factory(),
            'like_count' => 0,
            'love_count' => 0,
            'haha_count' => 0,
            'sad_count' => 0,
            'angry_count' => 0,
            'care_count' => 0,
        ];
    }
}
