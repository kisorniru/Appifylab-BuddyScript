<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostContent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostContentFactory extends Factory
{
    protected $model = PostContent::class;

    public function definition(): array
    {
        $body = $this->faker->paragraphs($this->faker->numberBetween(1, 3), true);

        return [
            'post_id' => Post::factory(),
            'title' => null,
            'body' => $body,
            'excerpt' => Str::limit($body, 120),
            'metadata' => null,
        ];
    }
}
