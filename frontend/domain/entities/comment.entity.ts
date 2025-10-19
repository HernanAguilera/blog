/**
 * Comment Entity
 *
 * Entidad de dominio que representa un comentario en el blog
 */

import { CommentId } from '../value-objects/comment-id.vo';
import { CommentContent } from '../value-objects/comment-content.vo';
import { CommentStatus } from '../value-objects/comment-status.vo';
import { CommentAuthorType } from '../value-objects/comment-author-type.vo';
import { PostId } from '../value-objects/post-id.vo';
import type { CommentData } from '../types/comment.types';

export class Comment {
  private constructor(
    private readonly id: CommentId,
    private readonly postId: PostId,
    private readonly content: CommentContent,
    private readonly status: CommentStatus,
    private readonly authorType: CommentAuthorType,
    private readonly userId: number | null,
    private readonly userName: string | null,
    private readonly anonymousName: string | null,
    private readonly anonymousEmail: string | null,
    private readonly parentId: CommentId | null,
    private readonly createdAt: Date,
    private readonly updatedAt: Date,
    private readonly moderatorId: number | null = null,
    private readonly moderatedAt: Date | null = null
  ) {}

  /**
   * Crea una nueva instancia de Comment desde datos del API
   */
  public static fromData(data: CommentData): Comment {
    return new Comment(
      CommentId.create(data.id),
      new PostId(data.postId.toString()),
      CommentContent.create(data.content),
      CommentStatus.create(data.status),
      CommentAuthorType.create(data.authorType),
      data.userId ?? null,
      data.userName ?? null,
      data.anonymousName ?? null,
      data.anonymousEmail ?? null,
      data.parentId ? CommentId.create(data.parentId) : null,
      new Date(data.createdAt),
      new Date(data.updatedAt),
      data.moderatorId ?? null,
      data.moderatedAt ? new Date(data.moderatedAt) : null
    );
  }

  // Getters para ID
  public getId(): CommentId {
    return this.id;
  }

  public getIdValue(): string {
    return this.id.getValue();
  }

  // Getters para Post
  public getPostId(): PostId {
    return this.postId;
  }

  public getPostIdValue(): number {
    return Number(this.postId.value());
  }

  // Getters para Content
  public getContent(): CommentContent {
    return this.content;
  }

  public getContentValue(): string {
    return this.content.getValue();
  }

  // Getters para Status
  public getStatus(): CommentStatus {
    return this.status;
  }

  public getStatusValue(): string {
    return this.status.getValue();
  }

  // Getters para Author Type
  public getAuthorType(): CommentAuthorType {
    return this.authorType;
  }

  public getAuthorTypeValue(): string {
    return this.authorType.getValue();
  }

  // Getters para User Info
  public getUserId(): number | null {
    return this.userId;
  }

  public getUserName(): string | null {
    return this.userName;
  }

  // Getters para Anonymous Info
  public getAnonymousName(): string | null {
    return this.anonymousName;
  }

  public getAnonymousEmail(): string | null {
    return this.anonymousEmail;
  }

  // Getters para Parent
  public getParentId(): CommentId | null {
    return this.parentId;
  }

  public getParentIdValue(): string | null {
    return this.parentId?.getValue() ?? null;
  }

  // Getters para Dates
  public getCreatedAt(): Date {
    return this.createdAt;
  }

  public getUpdatedAt(): Date {
    return this.updatedAt;
  }

  public getModeratorId(): number | null {
    return this.moderatorId;
  }

  public getModeratedAt(): Date | null {
    return this.moderatedAt;
  }

  // Business Logic Methods

  /**
   * Verifica si el comentario está aprobado
   */
  public isApproved(): boolean {
    return this.status.isApproved();
  }

  /**
   * Verifica si el comentario está pendiente
   */
  public isPending(): boolean {
    return this.status.isPending();
  }

  /**
   * Verifica si el comentario está rechazado
   */
  public isRejected(): boolean {
    return this.status.isRejected();
  }

  /**
   * Verifica si el comentario está marcado como spam
   */
  public isSpam(): boolean {
    return this.status.isSpam();
  }

  /**
   * Verifica si el comentario es de un usuario registrado
   */
  public isFromUser(): boolean {
    return this.authorType.isUser();
  }

  /**
   * Verifica si el comentario es anónimo
   */
  public isAnonymous(): boolean {
    return this.authorType.isAnonymous();
  }

  /**
   * Verifica si el comentario es una respuesta
   */
  public isReply(): boolean {
    return this.parentId !== null;
  }

  /**
   * Verifica si el comentario es de nivel raíz
   */
  public isRoot(): boolean {
    return this.parentId === null;
  }

  /**
   * Verifica si el comentario ha sido moderado
   */
  public isModerated(): boolean {
    return this.moderatorId !== null && this.moderatedAt !== null;
  }

  /**
   * Obtiene el nombre del autor (usuario o anónimo)
   */
  public getAuthorName(): string {
    if (this.isFromUser()) {
      return this.userName ?? 'Usuario';
    }
    return this.anonymousName ?? 'Anónimo';
  }

  /**
   * Verifica si se puede responder a este comentario
   * (Solo comentarios aprobados pueden recibir respuestas)
   */
  public canReceiveReplies(): boolean {
    return this.isApproved();
  }

  /**
   * Convierte la entidad a un objeto plano (para serialización)
   */
  public toData(): CommentData {
    return {
      id: this.getIdValue(),
      postId: this.getPostIdValue(),
      content: this.getContentValue(),
      status: this.getStatusValue() as any,
      authorType: this.getAuthorTypeValue() as any,
      userId: this.userId ?? undefined,
      userName: this.userName ?? undefined,
      anonymousName: this.anonymousName ?? undefined,
      anonymousEmail: this.anonymousEmail ?? undefined,
      parentId: this.getParentIdValue() ?? undefined,
      createdAt: this.createdAt.toISOString(),
      updatedAt: this.updatedAt.toISOString(),
      moderatorId: this.moderatorId ?? undefined,
      moderatedAt: this.moderatedAt?.toISOString() ?? undefined,
    };
  }

  /**
   * Compara si dos comentarios son iguales (por ID)
   */
  public equals(other: Comment): boolean {
    return this.id.equals(other.id);
  }
}
