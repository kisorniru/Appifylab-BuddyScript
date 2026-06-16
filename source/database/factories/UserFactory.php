<?php

namespace Database\Factories;

use App\Constants\AccountStatus;
use App\Constants\AdminUser;
use App\Constants\SocialUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'name' => $this->faker->name(),
            'email' => 'temp-'.$this->faker->unique()->uuid().'@user.com',
            'email_verified_at' => now(),
            'password' => bcrypt('Pa$$word'),
            'phone' => $this->faker->phoneNumber(),
            'gender' => $this->faker->numberBetween(1, 2),
            'profile_picture' => 'https://picsum.photos/200/300',
            'is_admin' => AdminUser::FALSE,
            'is_social_user' => SocialUser::FALSE,
            'provider' => null,
            'provider_id' => null,
            'provider_unique_id' => null,
            'account_status' => AccountStatus::ACTIVE,
            'account_status_updated_at' => now(),
            'last_actived_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function social(): Factory
    {
        $providers = ['Google', 'Facebook', 'Apple'];
        $provider = $this->faker->randomElement($providers);

        return $this->state(function (array $attributes) use ($provider) {
            $providerId = $this->faker->unique()->randomNumber(9, true);

            return [
                'is_social_user' => SocialUser::TRUE,
                'provider' => $provider,
                'provider_id' => $providerId,
                'provider_unique_id' => $provider.'_'.$providerId,
                'password' => bcrypt(Str::random(10)),
            ];
        });
    }
}
