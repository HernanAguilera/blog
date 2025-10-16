<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\ModerateCommentRequest;
use Blog\Application\UseCases\Comment\ApproveCommentUseCase;
use Blog\Application\UseCases\Comment\RejectCommentUseCase;
use Blog\Application\UseCases\Comment\MarkCommentAsSpamUseCase;
use Blog\Application\UseCases\Comment\GetPendingCommentsUseCase;
use Blog\Application\UseCases\Comment\DeleteCommentUseCase;
use Blog\Domain\Comment\Exceptions\CommentNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCommentController extends Controller
{
    public function __construct(
        private readonly GetPendingCommentsUseCase $getPendingCommentsUseCase,
        private readonly ApproveCommentUseCase $approveCommentUseCase,
        private readonly RejectCommentUseCase $rejectCommentUseCase,
        private readonly MarkCommentAsSpamUseCase $markCommentAsSpamUseCase,
        private readonly DeleteCommentUseCase $deleteCommentUseCase
    ) {}

    /**
     * Get pending comments for moderation
     * GET /api/admin/comments/pending
     */
    public function pending(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->query('limit', 50);
            $offset = (int) $request->query('offset', 0);

            $result = $this->getPendingCommentsUseCase->execute($limit, $offset);

            return response()->json([
                'success' => true,
                'data' => [
                    'comments' => $result['comments'],
                    'total' => $result['total'],
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving pending comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a comment
     * PATCH /api/admin/comments/{id}/approve
     */
    public function approve(string $id, ModerateCommentRequest $request): JsonResponse
    {
        try {
            $this->approveCommentUseCase->execute($id, $request->getModeratorId());

            return response()->json([
                'success' => true,
                'message' => 'Comment approved successfully'
            ]);

        } catch (CommentNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a comment
     * PATCH /api/admin/comments/{id}/reject
     */
    public function reject(string $id, ModerateCommentRequest $request): JsonResponse
    {
        try {
            $this->rejectCommentUseCase->execute($id, $request->getModeratorId());

            return response()->json([
                'success' => true,
                'message' => 'Comment rejected successfully'
            ]);

        } catch (CommentNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark comment as spam
     * PATCH /api/admin/comments/{id}/spam
     */
    public function markAsSpam(string $id, ModerateCommentRequest $request): JsonResponse
    {
        try {
            $this->markCommentAsSpamUseCase->execute($id);

            return response()->json([
                'success' => true,
                'message' => 'Comment marked as spam successfully'
            ]);

        } catch (CommentNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking comment as spam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a comment
     * DELETE /api/admin/comments/{id}
     */
    public function destroy(string $id, ModerateCommentRequest $request): JsonResponse
    {
        try {
            $this->deleteCommentUseCase->execute($id);

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);

        } catch (CommentNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
