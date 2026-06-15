<?php

namespace Database\Factories;

use App\Constants\ReactableType;
use App\Constants\ReactionType;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReactionFactory extends Factory
{
    protected $model = Reaction::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reactable_type' => ReactableType::POST,
            'reactable_id' => 1,
            'reaction_type' => $this->faker->randomElement([
                ReactionType::LIKE,
                ReactionType::LOVE,
            ]),
        ];
    }
}
