<?php

namespace Database\Factories;

use App\Constants\MediaType;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostMediaFactory extends Factory
{
    protected $model = PostMedia::class;

    public function definition(): array
    {
        $width = $this->faker->randomElement([640, 800, 1024, 1280]);
        $height = $this->faker->randomElement([480, 720, 768, 960]);

        return [
            'post_id' => Post::factory(),
            'media_type' => MediaType::IMAGE,
            'file_url' => "https://picsum.photos/seed/{$this->faker->uuid()}/{$width}/{$height}",
            'thumbnail_url' => "https://picsum.photos/seed/{$this->faker->uuid()}/300/200",
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(80_000, 2_000_000),
            'width' => $width,
            'height' => $height,
            'duration' => null,
            'metadata' => null,
        ];
    }
}
