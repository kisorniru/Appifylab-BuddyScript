<?php

namespace Database\Factories;

use App\Constants\ContentStatus;
use App\Constants\PostType;
use App\Constants\PostVisibility;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->boolean(35) ? PostType::MEDIA : PostType::TEXT,
            'visibility' => $this->faker->boolean(85) ? PostVisibility::PUBLIC : PostVisibility::PRIVATE,
            'status' => ContentStatus::ACTIVE,
            'comments_count' => 0,
            'reactions_count' => 0,
            'shares_count' => 0,
        ];
    }

    public function public(): self
    {
        return $this->state(fn () => ['visibility' => PostVisibility::PUBLIC]);
    }

    public function private(): self
    {
        return $this->state(fn () => ['visibility' => PostVisibility::PRIVATE]);
    }

    public function text(): self
    {
        return $this->state(fn () => ['type' => PostType::TEXT]);
    }

    public function media(): self
    {
        return $this->state(fn () => ['type' => PostType::MEDIA]);
    }
}
