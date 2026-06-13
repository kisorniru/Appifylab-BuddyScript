<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->integer('gender')->nullable();
            $table->string('profile_picture')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_social_user')->default(false);
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider_unique_id')->unique()->nullable();
            $table->integer('account_status')->default(0);
            $table->timestamp('account_status_updated_at')->nullable();
            $table->timestamp('last_actived_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'gender',
                'profile_picture',
                'is_admin',
                'is_social_user',
                'provider',
                'provider_id',
                'provider_unique_id',
                'account_status',
                'account_status_updated_at',
                'last_actived_at',
                'deleted_at',
            ]);
        });
    }
};
