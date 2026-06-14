<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReactionStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'like_count',
        'love_count',
        'haha_count',
        'sad_count',
        'angry_count',
        'care_count',
    ];

    protected $casts = [
        'like_count' => 'integer',
        'love_count' => 'integer',
        'haha_count' => 'integer',
        'sad_count' => 'integer',
        'angry_count' => 'integer',
        'care_count' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
