<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageTranslationModel extends Model
{
    protected $table = 'page_translations';

    protected $fillable = [
        'page_id',
        'locale',
        'title',
        'content',
        'meta_description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(PageModel::class, 'page_id', 'id');
    }
}
