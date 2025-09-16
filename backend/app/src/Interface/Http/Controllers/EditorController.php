<?php

declare(strict_types=1);

namespace App\src\Interface\Http\Controllers;

use App\src\Application\DTOs\Post\AutoSavePostDTO;
use App\src\Application\DTOs\Post\PreviewPostDTO;
use App\src\Application\UseCases\Post\AutoSavePostUseCase;
use App\src\Application\UseCases\Post\PreviewPostUseCase;
use App\src\Interface\Http\Requests\AutoSavePostRequest;
use App\src\Interface\Http\Requests\PreviewPostRequest;
use App\src\Interface\Http\Resources\AutoSaveResponse;
use App\src\Interface\Http\Resources\PreviewResponse;
use App\src\Interface\Http\Resources\DraftResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EditorController
{
    public function __construct(
        private AutoSavePostUseCase $autoSavePostUseCase,
        private PreviewPostUseCase $previewPostUseCase
    ) {}

    /**
     * Auto-save post content
     */
    public function autoSave(AutoSavePostRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $dto = new AutoSavePostDTO(
                userId: Auth::id(),
                postId: $validated['post_id'] ?? null,
                title: $validated['title'],
                content: $validated['content'],
                excerpt: $validated['excerpt'] ?? null,
                slug: $validated['slug'] ?? null,
                status: $validated['status'] ?? 'draft',
                featuredImage: $validated['featured_image'] ?? null,
                metaDescription: $validated['meta_description'] ?? null,
                scheduledAt: $validated['scheduled_at'] ?? null
            );

            $result = $this->autoSavePostUseCase->execute($dto);

            return (new AutoSaveResponse($result))->response();

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error('Auto-save error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error auto-saving content',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate preview for post content
     */
    public function generatePreview(PreviewPostRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $dto = new PreviewPostDTO(
                userId: Auth::id(),
                title: $validated['title'],
                content: $validated['content'],
                excerpt: $validated['excerpt'] ?? null,
                slug: $validated['slug'] ?? null,
                status: $validated['status'] ?? 'draft',
                featuredImage: $validated['featured_image'] ?? null,
                metaDescription: $validated['meta_description'] ?? null,
                scheduledAt: $validated['scheduled_at'] ?? null,
                ttlHours: $validated['ttl_hours'] ?? 24
            );

            $result = $this->previewPostUseCase->execute($dto);

            return (new PreviewResponse($result))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error('Preview generation error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error generating preview',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Show preview content using token (public access)
     */
    public function showPreview(string $token): Response
    {
        try {
            // Find preview by token
            $preview = \DB::table('post_previews')
                ->where('token', $token)
                ->where('expires_at', '>', now())
                ->first();

            if (!$preview) {
                return response()->view('errors.preview-expired', [], 404);
            }

            // Sanitize content for preview display
            $htmlSanitizer = app(\App\src\Domain\Post\Services\HtmlSanitizerInterface::class);
            $sanitizedContent = $htmlSanitizer->sanitize($preview->content);

            // Prepare preview data
            $previewData = [
                'title' => $preview->title,
                'content' => $sanitizedContent,
                'excerpt' => $preview->excerpt,
                'meta_description' => $preview->meta_description,
                'expires_at' => $preview->expires_at,
                'is_preview' => true
            ];

            return response()->view('preview.show', $previewData);

        } catch (\Exception $e) {
            Log::warning('Preview display error', [
                'token' => substr($token, 0, 8) . '...',
                'error' => $e->getMessage()
            ]);

            return response()->view('errors.preview-error', [], 500);
        }
    }

    /**
     * Get auto-saved drafts for current user
     */
    public function getDrafts(Request $request): JsonResponse
    {
        try {
            $drafts = \DB::table('post_drafts')
                ->where('user_id', Auth::id())
                ->where('expires_at', '>', now())
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            return DraftResource::collection($drafts);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving drafts',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Restore auto-saved draft
     */
    public function restoreDraft(int $draftId): JsonResponse
    {
        try {
            $draft = \DB::table('post_drafts')
                ->where('id', $draftId)
                ->where('user_id', Auth::id())
                ->where('expires_at', '>', now())
                ->first();

            if (!$draft) {
                return response()->json([
                    'message' => 'Draft not found or expired'
                ], Response::HTTP_NOT_FOUND);
            }

            return new DraftResource($draft);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error restoring draft',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}