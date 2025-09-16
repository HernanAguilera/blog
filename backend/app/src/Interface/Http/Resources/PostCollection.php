<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public $collects = PostResource::class;

    private array $pagination;

    public function __construct($resource, array $pagination = [])
    {
        $this->pagination = $pagination;
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'pagination' => [
                    'total' => $this->pagination['total'] ?? 0,
                    'per_page' => $this->pagination['perPage'] ?? 15,
                    'current_page' => $this->pagination['page'] ?? 1,
                    'last_page' => $this->pagination['total'] > 0
                        ? (int) ceil($this->pagination['total'] / ($this->pagination['perPage'] ?? 15))
                        : 1,
                    'from' => $this->getFromValue(),
                    'to' => $this->getToValue(),
                ]
            ]
        ];
    }

    private function getFromValue(): int
    {
        $page = $this->pagination['page'] ?? 1;
        $perPage = $this->pagination['perPage'] ?? 15;
        $total = $this->pagination['total'] ?? 0;

        if ($total === 0) {
            return 0;
        }

        return ($page - 1) * $perPage + 1;
    }

    private function getToValue(): int
    {
        $page = $this->pagination['page'] ?? 1;
        $perPage = $this->pagination['perPage'] ?? 15;
        $total = $this->pagination['total'] ?? 0;

        if ($total === 0) {
            return 0;
        }

        $to = $page * $perPage;
        return min($to, $total);
    }
}