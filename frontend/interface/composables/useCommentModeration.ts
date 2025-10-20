/**
 * useCommentModeration Composable
 *
 * Hook para gestión de moderación de comentarios (admin)
 */

import { ref, onMounted, type Ref } from 'vue';
import { useCommentStore } from '../stores/comment.store';
import container from '../../shared/container/simple-container';
import type { Comment } from '../../domain/entities/comment.entity';
import type { GetPendingCommentsUseCase } from '../../application/use-cases/comment/get-pending-comments.use-case';
import type { ApproveCommentUseCase } from '../../application/use-cases/comment/approve-comment.use-case';
import type { RejectCommentUseCase } from '../../application/use-cases/comment/reject-comment.use-case';
import type { MarkCommentAsSpamUseCase } from '../../application/use-cases/comment/mark-comment-as-spam.use-case';
import type { DeleteCommentUseCase } from '../../application/use-cases/comment/delete-comment.use-case';
import type { BulkApproveCommentsUseCase } from '../../application/use-cases/comment/bulk-approve-comments.use-case';
import type { BulkRejectCommentsUseCase } from '../../application/use-cases/comment/bulk-reject-comments.use-case';
import type { BulkDeleteCommentsUseCase } from '../../application/use-cases/comment/bulk-delete-comments.use-case';

export interface UseCommentModerationReturn {
  pendingComments: Ref<Comment[]>;
  loading: Ref<boolean>;
  error: Ref<string | null>;
  selectedIds: Ref<string[]>;
  loadPendingComments: () => Promise<void>;
  approveComment: (commentId: string) => Promise<void>;
  rejectComment: (commentId: string) => Promise<void>;
  markAsSpam: (commentId: string) => Promise<void>;
  deleteComment: (commentId: string) => Promise<void>;
  bulkApprove: (commentIds: string[]) => Promise<void>;
  bulkReject: (commentIds: string[]) => Promise<void>;
  bulkDelete: (commentIds: string[]) => Promise<void>;
  toggleSelection: (commentId: string) => void;
  clearSelection: () => void;
  selectAll: () => void;
}

export function useCommentModeration(): UseCommentModerationReturn {
  const commentStore = useCommentStore();

  // State
  const pendingComments = ref<Comment[]>([]) as Ref<Comment[]>;
  const loading = ref(false);
  const error = ref<string | null>(null);
  const selectedIds = ref<string[]>([]);

  /**
   * Carga los comentarios pendientes de moderación
   */
  const loadPendingComments = async (): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<GetPendingCommentsUseCase>('GetPendingCommentsUseCase');
      const result = await useCase.execute();

      // Actualizar store y estado local
      commentStore.setPendingComments(result);
      pendingComments.value = result;
    } catch (err: any) {
      error.value = err.message || 'Error al cargar comentarios pendientes';
      console.error('Error loading pending comments:', err);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Aprueba un comentario
   */
  const approveComment = async (commentId: string): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<ApproveCommentUseCase>('ApproveCommentUseCase');
      await useCase.execute(commentId);

      // Actualizar store
      commentStore.removePendingComment(commentId);

      // Recargar comentarios
      await loadPendingComments();
    } catch (err: any) {
      error.value = err.message || 'Error al aprobar el comentario';
      console.error('Error approving comment:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Rechaza un comentario
   */
  const rejectComment = async (commentId: string): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<RejectCommentUseCase>('RejectCommentUseCase');
      await useCase.execute(commentId);

      // Actualizar store
      commentStore.removePendingComment(commentId);

      // Recargar comentarios
      await loadPendingComments();
    } catch (err: any) {
      error.value = err.message || 'Error al rechazar el comentario';
      console.error('Error rejecting comment:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Marca un comentario como spam
   */
  const markAsSpam = async (commentId: string): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<MarkCommentAsSpamUseCase>('MarkCommentAsSpamUseCase');
      await useCase.execute(commentId);

      // Actualizar store
      commentStore.removePendingComment(commentId);

      // Recargar comentarios
      await loadPendingComments();
    } catch (err: any) {
      error.value = err.message || 'Error al marcar como spam';
      console.error('Error marking comment as spam:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Elimina un comentario
   */
  const deleteComment = async (commentId: string): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<DeleteCommentUseCase>('DeleteCommentUseCase');
      await useCase.execute(commentId);

      // Actualizar store
      commentStore.removePendingComment(commentId);

      // Recargar comentarios
      await loadPendingComments();
    } catch (err: any) {
      error.value = err.message || 'Error al eliminar el comentario';
      console.error('Error deleting comment:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Aprueba múltiples comentarios
   */
  const bulkApprove = async (commentIds: string[]): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<BulkApproveCommentsUseCase>('BulkApproveCommentsUseCase');
      await useCase.execute(commentIds);

      // Recargar comentarios
      await loadPendingComments();

      // Limpiar selección
      clearSelection();
    } catch (err: any) {
      error.value = err.message || 'Error al aprobar los comentarios';
      console.error('Error bulk approving comments:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Rechaza múltiples comentarios
   */
  const bulkReject = async (commentIds: string[]): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<BulkRejectCommentsUseCase>('BulkRejectCommentsUseCase');
      await useCase.execute(commentIds);

      // Recargar comentarios
      await loadPendingComments();

      // Limpiar selección
      clearSelection();
    } catch (err: any) {
      error.value = err.message || 'Error al rechazar los comentarios';
      console.error('Error bulk rejecting comments:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Elimina múltiples comentarios
   */
  const bulkDelete = async (commentIds: string[]): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<BulkDeleteCommentsUseCase>('BulkDeleteCommentsUseCase');
      await useCase.execute(commentIds);

      // Recargar comentarios
      await loadPendingComments();

      // Limpiar selección
      clearSelection();
    } catch (err: any) {
      error.value = err.message || 'Error al eliminar los comentarios';
      console.error('Error bulk deleting comments:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Alterna la selección de un comentario
   */
  const toggleSelection = (commentId: string): void => {
    const index = selectedIds.value.indexOf(commentId);
    if (index > -1) {
      selectedIds.value.splice(index, 1);
    } else {
      selectedIds.value.push(commentId);
    }
  };

  /**
   * Limpia la selección
   */
  const clearSelection = (): void => {
    selectedIds.value = [];
  };

  /**
   * Selecciona todos los comentarios
   */
  const selectAll = (): void => {
    selectedIds.value = pendingComments.value.map((c) => c.getIdValue());
  };

  // Auto-load al montar
  onMounted(() => {
    loadPendingComments();
  });

  return {
    pendingComments,
    loading,
    error,
    selectedIds,
    loadPendingComments,
    approveComment,
    rejectComment,
    markAsSpam,
    deleteComment,
    bulkApprove,
    bulkReject,
    bulkDelete,
    toggleSelection,
    clearSelection,
    selectAll,
  };
}
