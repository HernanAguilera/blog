<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'title' => $this->title,
            'content' => $this->when($request->routeIs('*.restore'), $this->content),
            'excerpt' => $this->excerpt,
            'slug' => $this->slug,
            'status' => $this->status,
            'featured_image' => $this->featured_image,
            'meta_description' => $this->meta_description,
            'scheduled_at' => $this->scheduled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'expires_at' => $this->expires_at,
            'time_until_expiry' => $this->getTimeUntilExpiry(),
            'word_count' => $this->getWordCount(),
            'is_expired' => $this->isExpired()
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'type' => 'draft',
                'timestamp' => now()->toISOString(),
                'restore_available' => !$this->isExpired()
            ]
        ];
    }

    private function getTimeUntilExpiry(): ?string
    {
        if (!$this->expires_at) {
            return null;
        }

        $expiresAt = \Carbon\Carbon::parse($this->expires_at);

        if ($expiresAt->isPast()) {
            return 'expired';
        }

        return $expiresAt->diffForHumans();
    }

    private function getWordCount(): int
    {
        if (!$this->content) {
            return 0;
        }

        return str_word_count(strip_tags($this->content));
    }

    private function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return \Carbon\Carbon::parse($this->expires_at)->isPast();
    }
}