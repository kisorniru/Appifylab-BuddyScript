<?php

namespace App\Models;

use App\Constants\ReactableType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reactable_type',
        'reactable_id',
        'reaction_type',
    ];

    protected $casts = [
        'reactable_type' => 'integer',
        'reactable_id' => 'integer',
        'reaction_type' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'reactable_id')
            ->where('reactable_type', ReactableType::POST);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'reactable_id')
            ->where('reactable_type', ReactableType::COMMENT);
    }

    public function scopeForPost($query, int $postId)
    {
        return $query->where('reactable_type', ReactableType::POST)
            ->where('reactable_id', $postId);
    }

    public function scopeForComment($query, int $commentId)
    {
        return $query->where('reactable_type', ReactableType::COMMENT)
            ->where('reactable_id', $commentId);
    }
}
