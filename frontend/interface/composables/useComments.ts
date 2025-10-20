/**
 * useComments Composable
 *
 * Hook para gestión de comentarios en páginas públicas
 */

import { ref, onMounted, type Ref } from 'vue';
import { useCommentStore } from '../stores/comment.store';
import container from '../../shared/container/simple-container';
import type { CommentTreeNode, CreateCommentData, CreateAnonymousCommentData } from '../../domain/types/comment.types';
import type { GetCommentsTreeUseCase } from '../../application/use-cases/comment/get-comments-tree.use-case';
import type { GetCommentCountUseCase } from '../../application/use-cases/comment/get-comment-count.use-case';
import type { CreateCommentUseCase } from '../../application/use-cases/comment/create-comment.use-case';
import type { CreateAnonymousCommentUseCase } from '../../application/use-cases/comment/create-anonymous-comment.use-case';

export interface UseCommentsReturn {
  comments: Ref<CommentTreeNode[]>;
  commentCount: Ref<number>;
  loading: Ref<boolean>;
  error: Ref<string | null>;
  loadComments: () => Promise<void>;
  createComment: (data: CreateCommentData) => Promise<void>;
  createAnonymousComment: (data: CreateAnonymousCommentData) => Promise<void>;
  refreshComments: () => Promise<void>;
}

export function useComments(postSlug: string): UseCommentsReturn {
  const commentStore = useCommentStore();

  // State
  const comments = ref<CommentTreeNode[]>([]);
  const commentCount = ref<number>(0);
  const loading = ref(false);
  const error = ref<string | null>(null);

  /**
   * Carga los comentarios del post
   */
  const loadComments = async (): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<GetCommentsTreeUseCase>('GetCommentsTreeUseCase');
      const result = await useCase.execute(postSlug);

      // Actualizar store y estado local
      commentStore.setCommentsForPost(postSlug, result);
      comments.value = result;

      // Obtener contador
      const countUseCase = container.get<GetCommentCountUseCase>('GetCommentCountUseCase');
      commentCount.value = await countUseCase.execute(postSlug);
    } catch (err: any) {
      error.value = err.message || 'Error al cargar los comentarios';
      console.error('Error loading comments:', err);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Crea un comentario como usuario registrado
   */
  const createComment = async (data: CreateCommentData): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<CreateCommentUseCase>('CreateCommentUseCase');
      await useCase.execute(data);

      // Recargar comentarios
      await loadComments();
    } catch (err: any) {
      error.value = err.message || 'Error al crear el comentario';
      console.error('Error creating comment:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Crea un comentario anónimo
   */
  const createAnonymousComment = async (data: CreateAnonymousCommentData): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      const useCase = container.get<CreateAnonymousCommentUseCase>('CreateAnonymousCommentUseCase');
      await useCase.execute(data);

      // Recargar comentarios
      await loadComments();
    } catch (err: any) {
      error.value = err.message || 'Error al crear el comentario';
      console.error('Error creating anonymous comment:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Recarga los comentarios (alias de loadComments)
   */
  const refreshComments = async (): Promise<void> => {
    await loadComments();
  };

  // Auto-load al montar
  onMounted(() => {
    loadComments();
  });

  return {
    comments,
    commentCount,
    loading,
    error,
    loadComments,
    createComment,
    createAnonymousComment,
    refreshComments,
  };
}
