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
  success: boolean;
  data: {
    comments: CommentTreeNode[];
    total: number;
  };
};

type CommentResponse = {
  success: boolean;
  data: CommentData;
  message?: string;
};

type CommentCountResponse = {
  success: boolean;
  data: {
    count: number;
  };
};

type CommentsListResponse = {
  success: boolean;
  data: {
    comments: CommentData[];
    total: number;
    limit?: number;
    offset?: number;
  };
};

type MessageResponse = {
  success: boolean;
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
      `/posts/${postSlug}/comments`
    );
    return response.data.comments;
  }

  /**
   * Obtiene el contador de comentarios aprobados para un post
   * GET /api/posts/{slug}/comments/count
   */
  async getCommentCount(postSlug: string): Promise<number> {
    const response = await this.httpClient.get<CommentCountResponse>(
      `/posts/${postSlug}/comments/count`
    );
    return response.data.count;
  }

  /**
   * Crea un comentario anónimo (requiere Turnstile token)
   * POST /api/posts/{slug}/comments/anonymous
   */
  async createAnonymousComment(data: CreateAnonymousCommentData): Promise<CommentData> {
    const payload: Record<string, any> = {
      content: data.content,
      author_name: data.anonymousName,
      author_email: data.anonymousEmail,
      'cf-turnstile-response': data.turnstileToken,
    };

    // Solo incluir parent_id si existe
    if (data.parentId) {
      payload.parent_id = data.parentId;
    }

    const response = await this.httpClient.post<CommentResponse>(
      `/posts/${data.postSlug}/comments/anonymous`,
      payload
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
    const payload: Record<string, any> = {
      content: data.content,
    };

    // Solo incluir parent_id si existe
    if (data.parentId) {
      payload.parent_id = data.parentId;
    }

    const response = await this.httpClient.post<CommentResponse>(
      `/posts/${data.postSlug}/comments`,
      payload
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
      '/admin/comments/pending'
    );
    return response.data.comments;
  }

  /**
   * Aprueba un comentario
   * PATCH /api/admin/comments/{id}/approve
   * Requiere: Authorization header con JWT token + rol admin
   */
  async approveComment(commentId: string): Promise<void> {
    await this.httpClient.patch<MessageResponse>(
      `/admin/comments/${commentId}/approve`,
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
      `/admin/comments/${commentId}/reject`,
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
      `/admin/comments/${commentId}/spam`,
      {}
    );
  }

  /**
   * Elimina un comentario
   * DELETE /api/admin/comments/{id}
   * Requiere: Authorization header con JWT token + rol admin
   */
  async deleteComment(commentId: string): Promise<void> {
    await this.httpClient.delete<MessageResponse>(`/admin/comments/${commentId}`);
  }

  /**
   * Aprueba múltiples comentarios
   * POST /api/admin/comments/bulk-approve
   * Requiere: Authorization header con JWT token + rol admin
   */
  async bulkApproveComments(commentIds: string[]): Promise<void> {
    await this.httpClient.post<MessageResponse>('/admin/comments/bulk-approve', {
      ids: commentIds,
    });
  }

  /**
   * Rechaza múltiples comentarios
   * POST /api/admin/comments/bulk-reject
   * Requiere: Authorization header con JWT token + rol admin
   */
  async bulkRejectComments(commentIds: string[]): Promise<void> {
    await this.httpClient.post<MessageResponse>('/admin/comments/bulk-reject', {
      ids: commentIds,
    });
  }

  /**
   * Elimina múltiples comentarios
   * POST /api/admin/comments/bulk-delete
   * Requiere: Authorization header con JWT token + rol admin
   */
  async bulkDeleteComments(commentIds: string[]): Promise<void> {
    await this.httpClient.post<MessageResponse>('/admin/comments/bulk-delete', {
      ids: commentIds,
    });
  }
}
