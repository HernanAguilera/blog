<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\CreateAnonymousCommentRequest;
use Blog\Application\UseCases\Comment\CreateCommentFromUserUseCase;
use Blog\Application\UseCases\Comment\CreateAnonymousCommentUseCase;
use Blog\Application\UseCases\Comment\GetCommentTreeUseCase;
use Blog\Application\DTOs\Comment\CreateCommentDTO;
use Blog\Application\DTOs\Comment\CreateAnonymousCommentDTO;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostSlug;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private readonly CreateCommentFromUserUseCase $createCommentFromUserUseCase,
        private readonly CreateAnonymousCommentUseCase $createAnonymousCommentUseCase,
        private readonly GetCommentTreeUseCase $getCommentTreeUseCase,
        private readonly PostRepositoryInterface $postRepository
    ) {}

    /**
     * Get comment tree for a post
     * GET /api/posts/{slug}/comments
     */
    public function index(string $slug): JsonResponse
    {
        try {
            // Find post by slug
            $post = $this->postRepository->findBySlug(new PostSlug($slug));

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // Get nested comment tree
            $commentTree = $this->getCommentTreeUseCase->execute($post->getId()->value());

            return response()->json([
                'success' => true,
                'data' => [
                    'comments' => $commentTree,
                    'total' => count($commentTree)
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error retrieving comments', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'slug' => $slug
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving comments',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Create comment from authenticated user
     * POST /api/posts/{slug}/comments
     */
    public function store(string $slug, CreateCommentRequest $request): JsonResponse
    {
        try {
            // Find post by slug
            $post = $this->postRepository->findBySlug(new PostSlug($slug));

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            $dto = new CreateCommentDTO(
                postId: $post->getId()->value(),
                userId: $request->getUserId(),
                content: $request->input('content'),
                parentId: $request->input('parent_id'),
                ipAddress: $request->getIpAddress(),
                userAgent: $request->getUserAgent()
            );

            $comment = $this->createCommentFromUserUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully',
                'data' => [
                    'comment_id' => $comment->id()->value(),
                    'status' => $comment->status()->value(),
                    'auto_approved' => $comment->status()->isApproved()
                ]
            ], 201);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Throwable $e) {
            \Log::error('Error creating comment from user', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'slug' => $slug
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating comment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Create anonymous comment
     * POST /api/posts/{slug}/comments/anonymous
     */
    public function storeAnonymous(string $slug, CreateAnonymousCommentRequest $request): JsonResponse
    {
        try {
            // Find post by slug
            $post = $this->postRepository->findBySlug(new PostSlug($slug));

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            $dto = new CreateAnonymousCommentDTO(
                postId: $post->getId()->value(),
                authorName: $request->input('author_name'),
                authorEmail: $request->input('author_email'),
                authorWebsite: $request->input('author_website'),
                content: $request->input('content'),
                parentId: $request->input('parent_id'),
                ipAddress: $request->getIpAddress(),
                userAgent: $request->getUserAgent(),
                turnstileToken: $request->getTurnstileToken()
            );

            $comment = $this->createAnonymousCommentUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Comment submitted successfully and is pending approval',
                'data' => [
                    'comment_id' => $comment->id()->value(),
                    'status' => $comment->status()->value(),
                    'pending_approval' => $comment->status()->isPending()
                ]
            ], 201);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Throwable $e) {
            \Log::error('Error creating anonymous comment', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'slug' => $slug
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating comment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get comment count for a post
     * GET /api/posts/{slug}/comments/count
     */
    public function count(string $slug): JsonResponse
    {
        try {
            $post = $this->postRepository->findBySlug(new PostSlug($slug));

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // This will be implemented when we add countApprovedByPostId to repository
            return response()->json([
                'success' => true,
                'data' => [
                    'count' => 0 // TODO: Implement count
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error counting comments', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'slug' => $slug
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error counting comments'
            ], 500);
        }
    }
}
