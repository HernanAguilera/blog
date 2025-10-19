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

    // Mapear recursivamente los comentarios del árbol a entidades
    return this.mapTreeNodes(tree);
  }

  /**
   * Mapea recursivamente los nodos del árbol a entidades Comment
   */
  private mapTreeNodes(nodes: CommentTreeNode[]): CommentTreeNode[] {
    return nodes.map((node) => ({
      comment: this.mapToCommentData(node.comment),
      replies: this.mapTreeNodes(node.replies),
      depth: node.depth,
    }));
  }

  /**
   * Asegura que CommentData tenga el formato correcto
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
   */
  async createComment(data: CreateCommentData): Promise<Comment> {
    const commentData = await this.commentAPI.createComment(data);
    return CommentEntity.fromData(this.mapToCommentData(commentData));
  }

  /**
   * Crea un comentario anónimo
   * Nota: Las validaciones de dominio deben hacerse en la UI antes de llamar al Use Case
   */
  async createAnonymousComment(data: CreateAnonymousCommentData): Promise<Comment> {
    const commentData = await this.commentAPI.createAnonymousComment(data);
    return CommentEntity.fromData(this.mapToCommentData(commentData));
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
