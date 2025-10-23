<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostModel extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'author_id',
        'status',
        'featured_image',
        'reading_time',
        'published_at',
        'scheduled_at',
    ];

    protected $casts = [
        'reading_time' => 'integer',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'author_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslationModel::class, 'post_id');
    }
}