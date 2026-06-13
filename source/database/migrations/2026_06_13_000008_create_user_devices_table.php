<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64);
            $table->string('device_token', 0);
            $table->integer('os_type');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('user_devices');
    }
};
