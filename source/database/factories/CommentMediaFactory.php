<?php

namespace Database\Factories;

use App\Constants\MediaType;
use App\Models\Comment;
use App\Models\CommentMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentMediaFactory extends Factory
{
    protected $model = CommentMedia::class;

    public function definition(): array
    {
        $width = $this->faker->randomElement([480, 640, 800]);
        $height = $this->faker->randomElement([360, 480, 600]);

        return [
            'comment_id' => Comment::factory(),
            'media_type' => MediaType::IMAGE,
            'file_url' => "https://picsum.photos/seed/{$this->faker->uuid()}/{$width}/{$height}",
            'thumbnail_url' => "https://picsum.photos/seed/{$this->faker->uuid()}/240/160",
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(50_000, 1_000_000),
            'width' => $width,
            'height' => $height,
            'duration' => null,
            'metadata' => null,
        ];
    }

    public function image(): self
    {
        return $this->state(function () {
            $width = $this->faker->randomElement([480, 640, 800]);
            $height = $this->faker->randomElement([360, 480, 600]);

            return [
                'media_type' => MediaType::IMAGE,
                'file_url' => "https://picsum.photos/seed/{$this->faker->uuid()}/{$width}/{$height}",
                'thumbnail_url' => "https://picsum.photos/seed/{$this->faker->uuid()}/240/160",
                'mime_type' => 'image/jpeg',
                'size' => $this->faker->numberBetween(50_000, 1_000_000),
                'width' => $width,
                'height' => $height,
                'duration' => null,
            ];
        });
    }

    public function voice(): self
    {
        return $this->state(fn () => [
            'media_type' => MediaType::AUDIO,
            'file_url' => "https://example.com/audio/comment-{$this->faker->uuid()}.webm",
            'thumbnail_url' => null,
            'mime_type' => 'audio/webm',
            'size' => $this->faker->numberBetween(20_000, 600_000),
            'width' => null,
            'height' => null,
            'duration' => $this->faker->numberBetween(3, 90),
        ]);
    }
}
