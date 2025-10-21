<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Application\Page\DTOs\PageResponseDTO;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var PageResponseDTO $page */
        $page = $this->resource;

        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'status' => $page->status,
            'translations' => $page->translations,
            'created_at' => $page->createdAt,
            'updated_at' => $page->updatedAt,
        ];
    }
}
