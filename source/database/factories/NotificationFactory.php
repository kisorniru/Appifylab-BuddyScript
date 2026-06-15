<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'type' => $this->faker->randomElement(['post_created', 'post_reacted', 'comment_created', 'reply_created', 'comment_reacted']),
            'notifiable_type' => null,
            'notifiable_id' => null,
            'data' => [
                'message' => $this->faker->sentence(),
            ],
            'read_at' => $this->faker->boolean(35) ? $this->faker->dateTimeBetween('-7 days') : null,
        ];
    }
}
