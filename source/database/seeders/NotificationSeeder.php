<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->where('is_admin', false)->get();

        if ($users->count() < 2) {
            return;
        }

        foreach ($users as $receiver) {
            for ($i = 0; $i < fake()->numberBetween(2, 5); $i++) {
                $sender = $users->where('id', '!=', $receiver->id)->random();

                Notification::factory()->create([
                    'receiver_id' => $receiver->id,
                    'sender_id' => $sender->id,
                ]);
            }
        }
    }
}
