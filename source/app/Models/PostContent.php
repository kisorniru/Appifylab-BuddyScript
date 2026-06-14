<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'title',
        'body',
        'excerpt',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
