/**
 * Comment Types
 *
 * Define tipos de datos para el dominio de comentarios
 * siguiendo las convenciones de TypeScript del proyecto.
 */

/**
 * Comment Status Enum
 * Estados posibles de un comentario
 */
export enum CommentStatus {
  PENDING = 'pending',
  APPROVED = 'approved',
  REJECTED = 'rejected',
  SPAM = 'spam',
}

/**
 * Comment Author Type Enum
 * Tipos de autores de comentarios
 */
export enum CommentAuthorType {
  USER = 'user',
  ANONYMOUS = 'anonymous',
}

/**
 * Comment Data Type
 * Representa los datos de un comentario del API
 */
export type CommentData = {
  id: string;
  postId: number;
  content: string;
  status: CommentStatus;
  authorType: CommentAuthorType;

  // Para usuarios registrados
  userId?: number;
  userName?: string;

  // Para usuarios anónimos
  anonymousName?: string;
  anonymousEmail?: string;

  // Para comentarios anidados
  parentId?: string;

  // Metadata
  createdAt: string;
  updatedAt: string;
  moderatorId?: number;
  moderatedAt?: string;
};

/**
 * Comment Author Data
 * Representa los datos del autor de un comentario
 */
export type CommentAuthorData = {
  type: 'registered' | 'anonymous';
  user_id?: number;
  name?: string;
  email?: string;
  website?: string | null;
};

/**
 * Comment Tree Node Type
 * Representa un nodo en el árbol de comentarios con sus respuestas.
 * Esta estructura coincide exactamente con la respuesta del backend.
 */
export type CommentTreeNode = {
  id: string;
  content: string;
  author: CommentAuthorData;
  depth: number;
  created_at: string;
  replies: CommentTreeNode[];
};

/**
 * Create Comment Data Type
 * Datos necesarios para crear un comentario como usuario registrado
 */
export type CreateCommentData = {
  postSlug: string;
  content: string;
  parentId?: string;
};

/**
 * Create Anonymous Comment Data Type
 * Datos necesarios para crear un comentario anónimo
 */
export type CreateAnonymousCommentData = {
  postSlug: string;
  content: string;
  anonymousName: string;
  anonymousEmail: string;
  turnstileToken: string;
  parentId?: string;
};

/**
 * Moderate Comment Data Type
 * Datos para moderar un comentario
 */
export type ModerateCommentData = {
  id: string;
  action: 'approve' | 'reject' | 'spam' | 'delete';
};

/**
 * Bulk Moderate Comments Data Type
 * Datos para acciones masivas de moderación
 */
export type BulkModerateCommentsData = {
  ids: string[];
  action: 'approve' | 'reject' | 'delete';
};

/**
 * Comment Stats Type
 * Estadísticas de comentarios
 */
export type CommentStats = {
  total: number;
  approved: number;
  pending: number;
  rejected: number;
  spam: number;
};
