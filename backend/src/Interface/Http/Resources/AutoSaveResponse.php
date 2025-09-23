<?php

declare(strict_types=1);

namespace Blog\Interface\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutoSaveResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'saved_at' => $this->resource['saved_at'],
            'expires_at' => $this->resource['expires_at'],
            'word_count' => $this->resource['word_count'],
            'reading_time' => $this->resource['reading_time'],
            'status' => 'success',
            'message' => 'Content auto-saved successfully'
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'auto_save' => true,
                'timestamp' => now()->toISOString(),
                'next_save_in' => config('editor.autosave_interval', 30) . ' seconds'
            ]
        ];
    }
}