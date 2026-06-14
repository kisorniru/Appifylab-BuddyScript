<?php

use App\Constants\PostType;
use App\Constants\PostVisibility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('viewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();

            $table->string('author_name');
            $table->string('author_avatar')->nullable();

            $table->tinyInteger('post_type')->unsigned()->default(PostType::TEXT);
            $table->tinyInteger('visibility')->unsigned()->default(PostVisibility::PUBLIC);

            $table->text('text_preview')->nullable();
            $table->string('media_preview_url')->nullable();

            $table->unsignedBigInteger('reaction_count')->default(0);
            $table->unsignedBigInteger('comment_count')->default(0);
            $table->unsignedBigInteger('share_count')->default(0);

            $table->tinyInteger('viewer_reaction')->unsigned()->nullable();

            $table->timestamps();

            $table->unique(['viewer_id', 'post_id']);
            $table->index(['viewer_id', 'id']);
            $table->index(['viewer_id', 'created_at']);
            $table->index(['author_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_items');
    }
};
