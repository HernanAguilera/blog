<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use Src\Application\Page\UseCases\GetPublishedPageBySlugUseCase;
use Src\Application\Page\UseCases\GetPublishedPagesUseCase;
use Src\Domain\Page\Exceptions\PageNotFoundException;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function __construct(
        private readonly GetPublishedPagesUseCase $getPublishedPagesUseCase,
        private readonly GetPublishedPageBySlugUseCase $getPublishedPageBySlugUseCase
    ) {
    }

    /**
     * Get all published pages
     */
    public function index(): JsonResponse
    {
        $pages = $this->getPublishedPagesUseCase->execute();

        return response()->json([
            'data' => PageResource::collection($pages),
        ]);
    }

    /**
     * Get a published page by slug
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $page = $this->getPublishedPageBySlugUseCase->execute($slug);

            return response()->json([
                'data' => new PageResource($page),
            ]);
        } catch (PageNotFoundException $e) {
            return response()->json([
                'message' => 'Page not found',
            ], 404);
        }
    }
}
