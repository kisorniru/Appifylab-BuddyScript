<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'viewer_id',
        'post_id',
        'author_id',
        'author_name',
        'author_avatar',
        'post_type',
        'visibility',
        'text_preview',
        'media_preview_url',
        'reaction_count',
        'comment_count',
        'share_count',
        'viewer_reaction',
    ];

    protected $casts = [
        'viewer_id' => 'integer',
        'post_id' => 'integer',
        'author_id' => 'integer',
        'post_type' => 'integer',
        'visibility' => 'integer',
        'reaction_count' => 'integer',
        'comment_count' => 'integer',
        'share_count' => 'integer',
        'viewer_reaction' => 'integer',
    ];

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
