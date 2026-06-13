<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('active_histories', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 40);
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
        Schema::drop('active_histories');
    }
};
