<?php

use App\Constants\ContentStatus;
use App\Constants\PostType;
use App\Constants\PostVisibility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->tinyInteger('type')->unsigned()->default(PostType::TEXT);
            $table->tinyInteger('visibility')->unsigned()->default(PostVisibility::PUBLIC);
            $table->tinyInteger('status')->unsigned()->default(ContentStatus::ACTIVE);

            $table->unsignedBigInteger('comments_count')->default(0);
            $table->unsignedBigInteger('reactions_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['visibility', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
