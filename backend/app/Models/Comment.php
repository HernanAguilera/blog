<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'comments';

    protected $fillable = [
        'post_id',
        'user_id',
        'author_type',
        'author_name',
        'author_email',
        'author_website',
        'content',
        'status',
        'parent_id',
        'ip_address',
        'user_agent',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Get the user who wrote the comment (if registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the moderator who approved/rejected the comment.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scope to get only approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get only pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope to get only root comments (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if comment is from a registered user.
     */
    public function isFromRegisteredUser(): bool
    {
        return $this->author_type === 'registered' && $this->user_id !== null;
    }

    /**
     * Check if comment is anonymous.
     */
    public function isAnonymous(): bool
    {
        return $this->author_type === 'anonymous';
    }

    /**
     * Check if comment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if comment is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if comment is a reply.
     */
    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }
}
