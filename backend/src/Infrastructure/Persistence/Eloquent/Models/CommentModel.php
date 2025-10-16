<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Persistence\Eloquent\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommentModel extends Model
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

    public function post(): BelongsTo
    {
        return $this->belongsTo(PostModel::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CommentModel::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(CommentModel::class, 'parent_id');
    }
}
