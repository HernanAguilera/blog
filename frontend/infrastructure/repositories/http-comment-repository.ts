/**
 * HTTP Comment Repository
 *
 * Implementación del repositorio de comentarios usando HTTP API
 */

import type { CommentRepositoryInterface } from '../../domain/repositories/comment-repository.interface';
import type { Comment } from '../../domain/entities/comment.entity';
import type { CommentId } from '../../domain/value-objects/comment-id.vo';
import type {
  CommentTreeNode,
  CreateCommentData,
  CreateAnonymousCommentData,
  CommentStats,
  CommentData,
} from '../../domain/types/comment.types';
import { Comment as CommentEntity } from '../../domain/entities/comment.entity';
import type { CommentAPI } from '../api/comment-api';

export class HttpCommentRepository implements CommentRepositoryInterface {
  constructor(private readonly commentAPI: CommentAPI) {}

  /**
   * Obtiene el árbol de comentarios aprobados para un post
   */
  async getCommentTree(postSlug: string): Promise<CommentTreeNode[]> {
    const tree = await this.commentAPI.getCommentTree(postSlug);

    // Si el árbol está vacío, retornar array vacío
    if (!Array.isArray(tree) || tree.length === 0) {
      return [];
    }

    // El backend ya retorna la estructura correcta, solo retornamos tal cual
    return tree;
  }

  /**
   * Asegura que CommentData tenga el formato correcto
   * Se usa solo para comentarios individuales (no árbol)
   */
  private mapToCommentData(data: any): CommentData {
    return {
      id: data.id,
      postId: data.postId || data.post_id,
      content: data.content,
      status: data.status,
      authorType: data.authorType || data.author_type,
      userId: data.userId || data.user_id,
      userName: data.userName || data.user_name,
      anonymousName: data.anonymousName || data.anonymous_name,
      anonymousEmail: data.anonymousEmail || data.anonymous_email,
      parentId: data.parentId || data.parent_id,
      createdAt: data.createdAt || data.created_at,
      updatedAt: data.updatedAt || data.updated_at,
      moderatorId: data.moderatorId || data.moderator_id,
      moderatedAt: data.moderatedAt || data.moderated_at,
    };
  }

  /**
   * Obtiene el contador de comentarios
   */
  async getCommentCount(postSlug: string): Promise<number> {
    return await this.commentAPI.getCommentCount(postSlug);
  }

  /**
   * Obtiene estadísticas de comentarios
   * Nota: El backend actual no tiene este endpoint, retorna valores por defecto
   */
  async getCommentStats(postSlug: string): Promise<CommentStats> {
    // Por ahora solo tenemos el contador total
    const count = await this.getCommentCount(postSlug);

    return {
      total: count,
      approved: count,
      pending: 0,
      rejected: 0,
      spam: 0,
    };
  }

  /**
   * Crea un comentario como usuario registrado
   * Nota: Las validaciones de dominio deben hacerse en la UI antes de llamar al Use Case
   * El backend solo devuelve {comment_id, status, pending_approval}, no el objeto completo
   */
  async createComment(data: CreateCommentData): Promise<void> {
    await this.commentAPI.createComment(data);
    // No intentamos mapear a entidad porque el backend solo devuelve datos parciales
    // El caller debe refrescar la lista de comentarios si necesita ver el nuevo comentario
  }

  /**
   * Crea un comentario anónimo
   * Nota: Las validaciones de dominio deben hacerse en la UI antes de llamar al Use Case
   * El backend solo devuelve {comment_id, status, pending_approval}, no el objeto completo
   */
  async createAnonymousComment(data: CreateAnonymousCommentData): Promise<void> {
    await this.commentAPI.createAnonymousComment(data);
    // No intentamos mapear a entidad porque el backend solo devuelve datos parciales
    // El caller debe refrescar la lista de comentarios si necesita ver el nuevo comentario
  }

  /**
   * Obtiene comentarios pendientes (solo admin)
   */
  async getPendingComments(): Promise<Comment[]> {
    const comments = await this.commentAPI.getPendingComments();
    return comments.map((data) => CommentEntity.fromData(this.mapToCommentData(data)));
  }

  /**
   * Aprueba un comentario (solo admin)
   */
  async approveComment(id: CommentId): Promise<void> {
    await this.commentAPI.approveComment(id.getValue());
  }

  /**
   * Rechaza un comentario (solo admin)
   */
  async rejectComment(id: CommentId): Promise<void> {
    await this.commentAPI.rejectComment(id.getValue());
  }

  /**
   * Marca un comentario como spam (solo admin)
   */
  async markAsSpam(id: CommentId): Promise<void> {
    await this.commentAPI.markCommentAsSpam(id.getValue());
  }

  /**
   * Elimina un comentario (solo admin)
   */
  async deleteComment(id: CommentId): Promise<void> {
    await this.commentAPI.deleteComment(id.getValue());
  }

  /**
   * Aprueba múltiples comentarios (solo admin)
   */
  async bulkApprove(ids: CommentId[]): Promise<void> {
    const idStrings = ids.map((id) => id.getValue());
    await this.commentAPI.bulkApproveComments(idStrings);
  }

  /**
   * Rechaza múltiples comentarios (solo admin)
   */
  async bulkReject(ids: CommentId[]): Promise<void> {
    const idStrings = ids.map((id) => id.getValue());
    await this.commentAPI.bulkRejectComments(idStrings);
  }

  /**
   * Elimina múltiples comentarios (solo admin)
   */
  async bulkDelete(ids: CommentId[]): Promise<void> {
    const idStrings = ids.map((id) => id.getValue());
    await this.commentAPI.bulkDeleteComments(idStrings);
  }
}
