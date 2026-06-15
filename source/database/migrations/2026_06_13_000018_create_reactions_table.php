<?php

use App\Constants\ReactableType;
use App\Constants\ReactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->tinyInteger('reactable_type')->unsigned()->default(ReactableType::POST);
            $table->unsignedBigInteger('reactable_id');

            $table->tinyInteger('reaction_type')->unsigned()->default(ReactionType::LIKE);

            $table->timestamps();

            $table->unique(
                ['user_id', 'reactable_type', 'reactable_id'],
                'unique_user_reaction'
            );

            $table->index(['reactable_type', 'reactable_id']);
            $table->index(['user_id', 'reactable_type', 'reactable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
