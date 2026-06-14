<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_reaction_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->unique()->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('like_count')->default(0);
            $table->unsignedBigInteger('love_count')->default(0);
            $table->unsignedBigInteger('haha_count')->default(0);
            $table->unsignedBigInteger('sad_count')->default(0);
            $table->unsignedBigInteger('angry_count')->default(0);
            $table->unsignedBigInteger('care_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reaction_stats');
    }
};
