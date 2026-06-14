<?php

namespace Database\Factories;

use App\Constants\PostType;
use App\Constants\PostVisibility;
use App\Models\FeedItem;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedItemFactory extends Factory
{
    protected $model = FeedItem::class;

    public function definition(): array
    {
        return [
            'viewer_id' => User::factory(),
            'post_id' => Post::factory(),
            'author_id' => User::factory(),
            'author_name' => $this->faker->name(),
            'author_avatar' => 'https://picsum.photos/200/200',
            'post_type' => PostType::TEXT,
            'visibility' => PostVisibility::PUBLIC,
            'text_preview' => $this->faker->sentence(12),
            'media_preview_url' => null,
            'reaction_count' => 0,
            'comment_count' => 0,
            'share_count' => 0,
            'viewer_reaction' => null,
        ];
    }
}
