<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'media_type',
        'file_url',
        'thumbnail_url',
        'mime_type',
        'size',
        'width',
        'height',
        'duration',
        'metadata',
    ];

    protected $casts = [
        'media_type' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration' => 'integer',
        'metadata' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
