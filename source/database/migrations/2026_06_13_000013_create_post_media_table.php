<?php

use App\Constants\MediaType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();

            $table->tinyInteger('media_type')->unsigned()->default(MediaType::IMAGE);
            $table->string('file_url');
            $table->string('thumbnail_url')->nullable();
            $table->string('mime_type')->nullable();

            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('post_id');
            $table->index('media_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_media');
    }
};
