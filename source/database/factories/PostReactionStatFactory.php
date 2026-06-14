<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostReactionStat;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostReactionStatFactory extends Factory
{
    protected $model = PostReactionStat::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'like_count' => 0,
            'love_count' => 0,
            'haha_count' => 0,
            'sad_count' => 0,
            'angry_count' => 0,
            'care_count' => 0,
        ];
    }
}
