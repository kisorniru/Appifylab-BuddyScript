<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_errors', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('ip_address', 0)->nullable();
            $table->string('os_version', 0)->nullable();
            $table->integer('os_type')->nullable();
            $table->string('device_name', 0)->nullable();
            $table->string('app_version', 0)->nullable();
            $table->integer('app_version_code')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('login_errors');
    }
};
