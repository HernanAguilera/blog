/**
 * Comment API Client
 *
 * Cliente para interactuar con los endpoints de comentarios del backend
 * Maneja rutas públicas, autenticadas y admin
 */

import type { HttpClientInterface } from '../services/http-client.interface';
import type {
  CommentData,
  CommentTreeNode,
  CreateCommentData,
  CreateAnonymousCommentData,
  CommentStats,
} from '../../domain/types/comment.types';

/**
 * Response types del API
 */
type CommentTreeResponse = {
  data: CommentTreeNode[];
};

type CommentResponse = {
  data: CommentData;
  message?: string;
};

type CommentCountResponse = {
  data: {
    count: number;
  };
};

type CommentsListResponse = {
  data: CommentData[];
};

type MessageResponse = {
  message: string;
};

export class CommentAPI {
  constructor(private readonly httpClient: HttpClientInterface) {}

  // ========================================
  // ENDPOINTS PÚBLICOS (sin autenticación)
  // ========================================

  /**
   * Obtiene el árbol de comentarios aprobados para un post
   * GET /api/posts/{slug}/comments
   */
  async getCommentTree(postSlug: string): Promise<CommentTreeNode[]> {
    const response = await this.httpClient.get<CommentTreeResponse>(
      `/api/posts/${postSlug}/comments`
    );
    return response.data;
  }

  /**
   * Obtiene el contador de comentarios aprobados para un post
   * GET /api/posts/{slug}/comments/count
   */
  async getCommentCount(postSlug: string): Promise<number> {
    const response = await this.httpClient.get<CommentCountResponse>(
      `/api/posts/${postSlug}/comments/count`
    );
    return response.data.count;
  }

  /**
   * Crea un comentario anónimo (requiere Turnstile token)
   * POST /api/posts/{slug}/comments/anonymous
   */
  async createAnonymousComment(data: CreateAnonymousCommentData): Promise<CommentData> {
    const response = await this.httpClient.post<CommentResponse>(
      `/api/posts/${data.postSlug}/comments/anonymous`,
      {
        content: data.content,
        anonymous_name: data.anonymousName,
        anonymous_email: data.anonymousEmail,
        turnstile_token: data.turnstileToken,
        parent_id: data.parentId,
      }
    );
    return response.data;
  }

  // ========================================
  // ENDPOINTS AUTENTICADOS (requieren JWT)
  // ========================================

  /**
   * Crea un comentario como usuario registrado
   * POST /api/posts/{slug}/comments
   * Requiere: Authorization header con JWT token
   */
  async createComment(data: CreateCommentData): Promise<CommentData> {
    const response = await this.httpClient.post<CommentResponse>(
      `/api/posts/${data.postSlug}/comments`,
      {
        content: data.content,
        parent_id: data.parentId,
      }
    );
    return response.data;
  }

  // ========================================
  // ENDPOINTS ADMIN (requieren JWT + rol admin)
  // ========================================

  /**
   * Obtiene comentarios pendientes de moderación
   * GET /api/admin/comments/pending
   * Requiere: Authorization header con JWT token + rol admin
   */
  async getPendingComments(): Promise<CommentData[]> {
    const response = await this.httpClient.get<CommentsListResponse>(
      '/api/admin/comments/pending'
    );
    return response.data;
  }

  /**
   * Aprueba un comentario
   * PATCH /api/admin/comments/{id}/approve
   * Requiere: Authorization header con JWT token + rol admin
   */
  async approveComment(commentId: string): Promise<void> {
    await this.httpClient.patch<MessageResponse>(
      `/api/admin/comments/${commentId}/approve`,
      {}
    );
  }

  /**
   * Rechaza un comentario
   * PATCH /api/admin/comments/{id}/reject
   * Requiere: Authorization header con JWT token + rol admin
   */
  async rejectComment(commentId: string): Promise<void> {
    await this.httpClient.patch<MessageResponse>(
      `/api/admin/comments/${commentId}/reject`,
      {}
    );
  }

  /**
   * Marca un comentario como spam
   * PATCH /api/admin/comments/{id}/spam
   * Requiere: Authorization header con JWT token + rol admin
   */
  async markCommentAsSpam(commentId: string): Promise<void> {
    await this.httpClient.patch<MessageResponse>(
      `/api/admin/comments/${commentId}/spam`,
      {}
    );
  }

  /**
   * Elimina un comentario
   * DELETE /api/admin/comments/{id}
   * Requiere: Authorization header con JWT token + rol admin
   */
  async deleteComment(commentId: string): Promise<void> {
    await this.httpClient.delete<MessageResponse>(`/api/admin/comments/${commentId}`);
  }

  /**
   * Aprueba múltiples comentarios
   * POST /api/admin/comments/bulk-approve
   * Requiere: Authorization header con JWT token + rol admin
   */
  async bulkApproveComments(commentIds: string[]): Promise<void> {
    await this.httpClient.post<MessageResponse>('/api/admin/comments/bulk-approve', {
      ids: commentIds,
    });
  }

  /**
   * Rechaza múltiples comentarios
   * POST /api/admin/comments/bulk-reject
   * Requiere: Authorization header con JWT token + rol admin
   */
  async bulkRejectComments(commentIds: string[]): Promise<void> {
    await this.httpClient.post<MessageResponse>('/api/admin/comments/bulk-reject', {
      ids: commentIds,
    });
  }

  /**
   * Elimina múltiples comentarios
   * POST /api/admin/comments/bulk-delete
   * Requiere: Authorization header con JWT token + rol admin
   */
  async bulkDeleteComments(commentIds: string[]): Promise<void> {
    await this.httpClient.post<MessageResponse>('/api/admin/comments/bulk-delete', {
      ids: commentIds,
    });
  }
}
