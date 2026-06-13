<?php

namespace Database\Seeders;

use App\Constants\AccountStatus;
use App\Constants\AdminUser;
use App\Constants\GenderType;
use App\Constants\SocialUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@buddyscript.us'],
            [
                'name' => 'Admin User',
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt('Pa$$word'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone' => '+8801725156188',
                'gender' => GenderType::MALE,
                'profile_picture' => 'https://picsum.photos/200/300',
                'is_admin' => AdminUser::TRUE,
                'is_social_user' => SocialUser::FALSE,
                'provider' => null,
                'provider_id' => null,
                'account_status' => AccountStatus::ACTIVE,
                'account_status_updated_at' => Carbon::now(),
                'last_actived_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
