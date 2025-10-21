/**
 * Comment Repository Interface
 *
 * Define el contrato para el repositorio de comentarios
 * siguiendo las convenciones del proyecto (interfaces para abstracciones)
 */

import type { Comment } from '../entities/comment.entity';
import type { CommentId } from '../value-objects/comment-id.vo';
import type {
  CommentTreeNode,
  CreateCommentData,
  CreateAnonymousCommentData,
  CommentStats,
} from '../types/comment.types';

export interface CommentRepositoryInterface {
  /**
   * Obtiene el árbol de comentarios para un post
   * @param postSlug - Slug del post
   * @returns Árbol de comentarios con estructura anidada
   */
  getCommentTree(postSlug: string): Promise<CommentTreeNode[]>;

  /**
   * Obtiene el número total de comentarios aprobados para un post
   * @param postSlug - Slug del post
   * @returns Número de comentarios
   */
  getCommentCount(postSlug: string): Promise<number>;

  /**
   * Obtiene estadísticas de comentarios para un post
   * @param postSlug - Slug del post
   * @returns Estadísticas de comentarios
   */
  getCommentStats(postSlug: string): Promise<CommentStats>;

  /**
   * Crea un nuevo comentario como usuario registrado
   * @param data - Datos del comentario
   * @returns Promise que se resuelve cuando el comentario se crea exitosamente
   * Nota: El backend devuelve datos parciales, se debe refrescar la lista para ver el comentario
   */
  createComment(data: CreateCommentData): Promise<void>;

  /**
   * Crea un nuevo comentario anónimo
   * @param data - Datos del comentario anónimo (incluye Turnstile token)
   * @returns Promise que se resuelve cuando el comentario se crea exitosamente
   * Nota: El backend devuelve datos parciales, se debe refrescar la lista para ver el comentario
   */
  createAnonymousComment(data: CreateAnonymousCommentData): Promise<void>;

  /**
   * Obtiene todos los comentarios pendientes de moderación
   * Solo accesible para administradores
   * @returns Lista de comentarios pendientes
   */
  getPendingComments(): Promise<Comment[]>;

  /**
   * Aprueba un comentario
   * Solo accesible para administradores
   * @param id - ID del comentario
   */
  approveComment(id: CommentId): Promise<void>;

  /**
   * Rechaza un comentario
   * Solo accesible para administradores
   * @param id - ID del comentario
   */
  rejectComment(id: CommentId): Promise<void>;

  /**
   * Marca un comentario como spam
   * Solo accesible para administradores
   * @param id - ID del comentario
   */
  markAsSpam(id: CommentId): Promise<void>;

  /**
   * Elimina un comentario
   * Solo accesible para administradores
   * @param id - ID del comentario
   */
  deleteComment(id: CommentId): Promise<void>;

  /**
   * Aprueba múltiples comentarios
   * Solo accesible para administradores
   * @param ids - IDs de los comentarios
   */
  bulkApprove(ids: CommentId[]): Promise<void>;

  /**
   * Rechaza múltiples comentarios
   * Solo accesible para administradores
   * @param ids - IDs de los comentarios
   */
  bulkReject(ids: CommentId[]): Promise<void>;

  /**
   * Elimina múltiples comentarios
   * Solo accesible para administradores
   * @param ids - IDs de los comentarios
   */
  bulkDelete(ids: CommentId[]): Promise<void>;
}
