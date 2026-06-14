<?php

namespace App\Models;

use App\Constants\ContentStatus;
use App\Constants\PostVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'visibility',
        'status',
        'comments_count',
        'reactions_count',
        'shares_count',
    ];

    protected $casts = [
        'type' => 'integer',
        'visibility' => 'integer',
        'status' => 'integer',
        'comments_count' => 'integer',
        'reactions_count' => 'integer',
        'shares_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function content(): HasOne
    {
        return $this->hasOne(PostContent::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reactionStats(): HasOne
    {
        return $this->hasOne(PostReactionStat::class);
    }

    public function feedItems(): HasMany
    {
        return $this->hasMany(FeedItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ContentStatus::ACTIVE);
    }

    public function scopeVisibleFor($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('visibility', PostVisibility::PUBLIC)
                ->orWhere(function ($q2) use ($userId) {
                    $q2->where('visibility', PostVisibility::PRIVATE)
                        ->where('user_id', $userId);
                });
        });
    }
}
