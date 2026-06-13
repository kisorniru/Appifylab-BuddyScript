<?php

namespace Database\Seeders;

use App\Constants\AdminUser;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        User::factory()->count(2)->create([
            'is_admin' => AdminUser::FALSE,
        ]);

        User::factory()->social()->count(2)->create([
            'is_admin' => AdminUser::FALSE,
        ]);
    }
}
