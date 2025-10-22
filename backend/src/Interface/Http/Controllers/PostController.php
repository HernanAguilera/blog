<?php

declare(strict_types=1);

namespace Blog\Interface\Http\Controllers;

use Blog\Application\DTOs\Post\CreatePostDTO;
use Blog\Application\DTOs\Post\UpdatePostDTO;
use Blog\Application\DTOs\Post\PostFilterDTO;
use Blog\Application\DTOs\Post\ChangePostStatusDTO;
use Blog\Application\UseCases\Post\CreatePostUseCase;
use Blog\Application\UseCases\Post\UpdatePostUseCase;
use Blog\Application\UseCases\Post\DeletePostUseCase;
use Blog\Application\UseCases\Post\GetPostUseCase;
use Blog\Application\UseCases\Post\GetPublishedPostsUseCase;
use Blog\Application\UseCases\Post\GetAllPostsUseCase;
use Blog\Application\UseCases\Post\ChangePostStatusUseCase;
use Blog\Application\UseCases\Post\GetPostTransitionsUseCase;
use Blog\Application\UseCases\Post\GetPostsByLocaleUseCase;
use Blog\Interface\Http\Requests\CreatePostRequest;
use Blog\Interface\Http\Requests\UpdatePostRequest;
use App\Http\Requests\ChangePostStatusRequest;
use Blog\Interface\Http\Resources\PostResource;
use Blog\Interface\Http\Resources\PostCollection;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\PostValidationException;
use Blog\Domain\Post\Exceptions\PostAccessDeniedException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController
{
    public function __construct(
        private CreatePostUseCase $createPostUseCase,
        private UpdatePostUseCase $updatePostUseCase,
        private DeletePostUseCase $deletePostUseCase,
        private GetPostUseCase $getPostUseCase,
        private GetPublishedPostsUseCase $getPublishedPostsUseCase,
        private GetAllPostsUseCase $getAllPostsUseCase,
        private ChangePostStatusUseCase $changePostStatusUseCase,
        private GetPostTransitionsUseCase $getPostTransitionsUseCase,
        private GetPostsByLocaleUseCase $getPostsByLocaleUseCase
    ) {}

    /**
     * Get public list of published posts
     * Supports filtering by locale via ?locale=es query parameter
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $locale = $request->query('locale');

            // If locale parameter is provided, use GetPostsByLocaleUseCase
            if ($locale) {
                $page = (int) $request->get('page', 1);
                $perPage = (int) $request->get('per_page', 15);

                $result = $this->getPostsByLocaleUseCase->execute($locale, $page, $perPage);

                return response()->json(
                    new PostCollection($result['posts'], $result)
                );
            }

            // Otherwise, use the default published posts query
            $filter = new PostFilterDTO(
                page: (int) $request->get('page', 1),
                perPage: (int) $request->get('per_page', 15),
                search: $request->get('search'),
                sortBy: $request->get('sort_by', 'created_at'),
                sortDirection: $request->get('sort_direction', 'desc')
            );

            $result = $this->getPublishedPostsUseCase->execute($filter);

            return response()->json(
                new PostCollection($result['posts'], $result)
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving posts',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single post by slug (public)
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $post = $this->getPostUseCase->executeBySlug($slug);

            // Only show published posts in public endpoint
            if (!$post->isPublished()) {
                return response()->json([
                    'message' => 'Post not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'data' => new PostResource($post)
            ]);

        } catch (PostNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving post',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get admin list of all posts (with filters)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $filter = new PostFilterDTO(
            page: (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 15),
            search: $request->get('search'),
            status: $request->get('status'),
            authorId: $request->get('author_id') ? (int) $request->get('author_id') : null,
            sortBy: $request->get('sort_by', 'created_at'),
            sortDirection: $request->get('sort_direction', 'desc')
        );

        try {
            $result = $this->getAllPostsUseCase->execute($filter);

            return response()->json(
                new PostCollection($result['posts'], $result)
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving posts',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single post by ID (admin)
     */
    public function adminShow(int $id): JsonResponse
    {
        try {
            $post = $this->getPostUseCase->executeById($id);

            return response()->json([
                'data' => new PostResource($post)
            ]);

        } catch (PostNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving post',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new post
     */
    public function store(CreatePostRequest $request): JsonResponse
    {
        try {
            $dto = new CreatePostDTO(
                title: $request->validated('title'),
                content: $request->validated('content'),
                excerpt: $request->validated('excerpt'),
                slug: $request->validated('slug'),
                status: $request->validated('status'),
                featuredImage: $request->validated('featured_image'),
                metaDescription: $request->validated('meta_description'),
                authorId: $request->getAuthorId(),
                scheduledAt: $request->validated('scheduled_at')
            );

            $post = $this->createPostUseCase->execute($dto);

            return response()->json([
                'message' => 'Post created successfully',
                'data' => new PostResource($post)
            ], Response::HTTP_CREATED);

        } catch (PostValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'error' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating post',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing post
     */
    public function update(int $id, UpdatePostRequest $request): JsonResponse
    {
        try {
            $dto = new UpdatePostDTO(
                id: $id,
                title: $request->validated('title'),
                content: $request->validated('content'),
                excerpt: $request->validated('excerpt'),
                slug: $request->validated('slug'),
                status: $request->validated('status'),
                featuredImage: $request->validated('featured_image'),
                metaDescription: $request->validated('meta_description'),
                scheduledAt: $request->validated('scheduled_at')
            );

            $post = $this->updatePostUseCase->execute($dto);

            return response()->json([
                'message' => 'Post updated successfully',
                'data' => new PostResource($post)
            ]);

        } catch (PostNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);

        } catch (PostValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'error' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating post',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a post
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->deletePostUseCase->execute($id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Failed to delete post'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'message' => 'Post deleted successfully'
            ]);

        } catch (PostNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting post',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change post status (transition)
     */
    public function changeStatus(int $id, ChangePostStatusRequest $request): JsonResponse
    {
        try {
            $dto = new ChangePostStatusDTO(
                postId: $id,
                userId: $request->getUserId(),
                newStatus: $request->validated('status')
            );

            $this->changePostStatusUseCase->execute($dto);

            // Get the updated post to return it
            $updatedPost = $this->getPostUseCase->executeById($id);

            return response()->json([
                'message' => 'Post status changed successfully',
                'data' => new PostResource($updatedPost)
            ]);

        } catch (PostNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);

        } catch (PostAccessDeniedException $e) {
            return response()->json([
                'message' => 'Access denied. You cannot change the status of this post.'
            ], Response::HTTP_FORBIDDEN);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Invalid status',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error changing post status',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available transitions for a post
     */
    public function getTransitions(int $id, Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $transitions = $this->getPostTransitionsUseCase->execute($id, $userId);

            return response()->json([
                'data' => $transitions
            ]);

        } catch (PostNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);

        } catch (PostAccessDeniedException $e) {
            return response()->json([
                'message' => 'Access denied'
            ], Response::HTTP_FORBIDDEN);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving transitions',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
