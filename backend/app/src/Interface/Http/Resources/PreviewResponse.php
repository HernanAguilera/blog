<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreviewResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'token' => $this->resource['token'],
            'preview_url' => url($this->resource['preview_url']),
            'full_preview_url' => url('/preview/' . $this->resource['token']),
            'expires_at' => $this->resource['expires_at'],
            'created_at' => $this->resource['created_at'],
            'status' => 'success',
            'message' => 'Preview generated successfully'
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'preview' => true,
                'timestamp' => now()->toISOString(),
                'security_note' => 'Preview URL is temporary and will expire',
                'share_instructions' => 'Share this URL to allow others to preview your content'
            ]
        ];
    }
}