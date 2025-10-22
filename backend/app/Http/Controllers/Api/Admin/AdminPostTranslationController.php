<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreatePostTranslationRequest;
use App\Http\Requests\Post\UpdatePostTranslationRequest;
use Blog\Application\UseCases\Post\CreatePostTranslationUseCase;
use Blog\Application\UseCases\Post\UpdatePostTranslationUseCase;
use Blog\Application\UseCases\Post\DeletePostTranslationUseCase;
use Blog\Application\UseCases\Post\GetPostTranslationsUseCase;
use Blog\Application\DTOs\Post\CreatePostTranslationDTO;
use Blog\Application\DTOs\Post\UpdatePostTranslationDTO;
use Illuminate\Http\JsonResponse;

class AdminPostTranslationController extends Controller
{
    public function __construct(
        private readonly CreatePostTranslationUseCase $createTranslationUseCase,
        private readonly UpdatePostTranslationUseCase $updateTranslationUseCase,
        private readonly DeletePostTranslationUseCase $deleteTranslationUseCase,
        private readonly GetPostTranslationsUseCase $getTranslationsUseCase
    ) {}

    /**
     * Get all translations for a post
     * GET /api/admin/posts/{id}/translations
     */
    public function index(string $postId): JsonResponse
    {
        try {
            $translations = $this->getTranslationsUseCase->execute($postId);

            return response()->json([
                'success' => true,
                'data' => $translations
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create a new translation for a post
     * POST /api/admin/posts/{id}/translations
     */
    public function store(string $postId, CreatePostTranslationRequest $request): JsonResponse
    {
        try {
            $dto = CreatePostTranslationDTO::fromArray([
                'post_id' => $postId,
                'locale' => $request->input('locale'),
                'title' => $request->input('title'),
                'slug' => $request->input('slug'),
                'content' => $request->input('content'),
                'excerpt' => $request->input('excerpt'),
                'meta_description' => $request->input('meta_description'),
            ]);

            $translation = $this->createTranslationUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Translation created successfully',
                'data' => $translation
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update an existing translation
     * PUT /api/admin/posts/{id}/translations/{locale}
     */
    public function update(
        string $postId,
        string $locale,
        UpdatePostTranslationRequest $request
    ): JsonResponse {
        try {
            $dto = UpdatePostTranslationDTO::fromArray([
                'post_id' => $postId,
                'locale' => $locale,
                'title' => $request->input('title'),
                'slug' => $request->input('slug'),
                'content' => $request->input('content'),
                'excerpt' => $request->input('excerpt'),
                'meta_description' => $request->input('meta_description'),
            ]);

            $translation = $this->updateTranslationUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Translation updated successfully',
                'data' => $translation
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete a translation
     * DELETE /api/admin/posts/{id}/translations/{locale}
     */
    public function destroy(string $postId, string $locale): JsonResponse
    {
        try {
            $this->deleteTranslationUseCase->execute($postId, $locale);

            return response()->json([
                'success' => true,
                'message' => 'Translation deleted successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
