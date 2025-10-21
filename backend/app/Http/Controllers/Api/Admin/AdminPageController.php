<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\CreatePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Http\Resources\PageResource;
use Src\Application\Page\DTOs\CreatePageDTO;
use Src\Application\Page\DTOs\UpdatePageDTO;
use Src\Application\Page\UseCases\CreatePageUseCase;
use Src\Application\Page\UseCases\DeletePageUseCase;
use Src\Application\Page\UseCases\GetAllPagesUseCase;
use Src\Application\Page\UseCases\GetPageBySlugUseCase;
use Src\Application\Page\UseCases\UpdatePageUseCase;
use Src\Domain\Page\Exceptions\PageAlreadyExistsException;
use Src\Domain\Page\Exceptions\PageNotFoundException;
use Illuminate\Http\JsonResponse;

class AdminPageController extends Controller
{
    public function __construct(
        private readonly GetAllPagesUseCase $getAllPagesUseCase,
        private readonly GetPageBySlugUseCase $getPageBySlugUseCase,
        private readonly CreatePageUseCase $createPageUseCase,
        private readonly UpdatePageUseCase $updatePageUseCase,
        private readonly DeletePageUseCase $deletePageUseCase
    ) {
    }

    /**
     * Get all pages (admin)
     */
    public function index(): JsonResponse
    {
        $pages = $this->getAllPagesUseCase->execute();

        return response()->json([
            'data' => PageResource::collection($pages),
        ]);
    }

    /**
     * Get a page by ID (admin)
     */
    public function show(string $id): JsonResponse
    {
        try {
            // We'll use slug for now, but in production you might want a separate GetPageByIdUseCase
            $page = $this->getPageBySlugUseCase->execute($id);

            return response()->json([
                'data' => new PageResource($page),
            ]);
        } catch (PageNotFoundException $e) {
            return response()->json([
                'message' => 'Page not found',
            ], 404);
        }
    }

    /**
     * Create a new page
     */
    public function store(CreatePageRequest $request): JsonResponse
    {
        try {
            $dto = new CreatePageDTO(
                slug: $request->input('slug'),
                status: $request->input('status'),
                translations: $request->input('translations')
            );

            $page = $this->createPageUseCase->execute($dto);

            return response()->json([
                'message' => 'Page created successfully',
                'data' => new PageResource($page),
            ], 201);
        } catch (PageAlreadyExistsException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    /**
     * Update a page
     */
    public function update(UpdatePageRequest $request, string $id): JsonResponse
    {
        try {
            $dto = new UpdatePageDTO(
                id: $id,
                slug: $request->input('slug'),
                status: $request->input('status'),
                translations: $request->input('translations')
            );

            $page = $this->updatePageUseCase->execute($dto);

            return response()->json([
                'message' => 'Page updated successfully',
                'data' => new PageResource($page),
            ]);
        } catch (PageNotFoundException $e) {
            return response()->json([
                'message' => 'Page not found',
            ], 404);
        }
    }

    /**
     * Delete a page
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->deletePageUseCase->execute($id);

            return response()->json([
                'message' => 'Page deleted successfully',
            ]);
        } catch (PageNotFoundException $e) {
            return response()->json([
                'message' => 'Page not found',
            ], 404);
        }
    }
}
