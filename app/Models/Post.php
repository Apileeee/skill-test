<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_draft',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the post is a draft.
     */
    public function isDraft(): bool
    {
        return $this->is_draft;
    }

    /**
     * Determine if the post is scheduled for future publication.
     */
    public function isScheduled(): bool
    {
        return ! $this->is_draft && $this->published_at && $this->published_at->isFuture();
    }

    /**
     * Determine if the post is published and active.
     */
    public function isPublished(): bool
    {
        return ! $this->is_draft && $this->published_at && $this->published_at->isPast();
    }

    /**
     * Scope to get only active (published) posts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_draft', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
