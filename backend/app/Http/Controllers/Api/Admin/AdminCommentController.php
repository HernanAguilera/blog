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
use Blog\Application\UseCases\Comment\BulkApproveCommentsUseCase;
use Blog\Application\UseCases\Comment\BulkRejectCommentsUseCase;
use Blog\Application\UseCases\Comment\BulkDeleteCommentsUseCase;
use Blog\Domain\Comment\Exceptions\CommentNotFoundException;
use Blog\Interface\Http\Resources\CommentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCommentController extends Controller
{
    public function __construct(
        private readonly GetPendingCommentsUseCase $getPendingCommentsUseCase,
        private readonly ApproveCommentUseCase $approveCommentUseCase,
        private readonly RejectCommentUseCase $rejectCommentUseCase,
        private readonly MarkCommentAsSpamUseCase $markCommentAsSpamUseCase,
        private readonly DeleteCommentUseCase $deleteCommentUseCase,
        private readonly BulkApproveCommentsUseCase $bulkApproveCommentsUseCase,
        private readonly BulkRejectCommentsUseCase $bulkRejectCommentsUseCase,
        private readonly BulkDeleteCommentsUseCase $bulkDeleteCommentsUseCase
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
                    'comments' => CommentResource::collection($result['comments'])->resolve(),
                    'total' => $result['total'],
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error retrieving pending comments', [
                'exception' => get_class($e),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving pending comments',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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

        } catch (\Throwable $e) {
            \Log::error('Error approving comment', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'comment_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error approving comment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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

        } catch (\Throwable $e) {
            \Log::error('Error rejecting comment', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'comment_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error rejecting comment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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

        } catch (\Throwable $e) {
            \Log::error('Error marking comment as spam', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'comment_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error marking comment as spam',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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

        } catch (\Throwable $e) {
            \Log::error('Error deleting comment', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'comment_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Bulk approve comments
     * POST /api/admin/comments/bulk-approve
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'comment_ids' => 'required|array|min:1|max:100',
            'comment_ids.*' => 'required|uuid|exists:comments,id'
        ]);

        try {
            $result = $this->bulkApproveCommentsUseCase->execute(
                $validated['comment_ids'],
                auth()->id()
            );

            $message = "Approved {$result['approved']} comment(s)";
            if (!empty($result['failed'])) {
                $message .= ", " . count($result['failed']) . " failed";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error in bulk approval', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'comment_ids' => $validated['comment_ids'] ?? []
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error in bulk approval',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Bulk reject comments
     * POST /api/admin/comments/bulk-reject
     */
    public function bulkReject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'comment_ids' => 'required|array|min:1|max:100',
            'comment_ids.*' => 'required|uuid|exists:comments,id'
        ]);

        try {
            $result = $this->bulkRejectCommentsUseCase->execute(
                $validated['comment_ids'],
                auth()->id()
            );

            $message = "Rejected {$result['rejected']} comment(s)";
            if (!empty($result['failed'])) {
                $message .= ", " . count($result['failed']) . " failed";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error in bulk rejection', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'comment_ids' => $validated['comment_ids'] ?? []
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error in bulk rejection',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Bulk delete comments
     * POST /api/admin/comments/bulk-delete
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'comment_ids' => 'required|array|min:1|max:100',
            'comment_ids.*' => 'required|uuid|exists:comments,id'
        ]);

        try {
            $result = $this->bulkDeleteCommentsUseCase->execute(
                $validated['comment_ids']
            );

            $message = "Deleted {$result['deleted']} comment(s)";
            if (!empty($result['failed'])) {
                $message .= ", " . count($result['failed']) . " failed";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error in bulk deletion', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'comment_ids' => $validated['comment_ids'] ?? []
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error in bulk deletion',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
