/**
 * CommentStatus Value Object
 *
 * Representa el estado de un comentario
 */

import { CommentStatus as CommentStatusEnum } from '../types/comment.types';

export class CommentStatus {
  private readonly value: CommentStatusEnum;

  private constructor(value: CommentStatusEnum) {
    this.value = value;
  }

  public static create(value: string): CommentStatus {
    if (!CommentStatus.isValid(value)) {
      throw new Error(
        `Invalid comment status: ${value}. Valid values are: ${Object.values(CommentStatusEnum).join(', ')}`
      );
    }

    return new CommentStatus(value as CommentStatusEnum);
  }

  public static createPending(): CommentStatus {
    return new CommentStatus(CommentStatusEnum.PENDING);
  }

  public static createApproved(): CommentStatus {
    return new CommentStatus(CommentStatusEnum.APPROVED);
  }

  public static createRejected(): CommentStatus {
    return new CommentStatus(CommentStatusEnum.REJECTED);
  }

  public static createSpam(): CommentStatus {
    return new CommentStatus(CommentStatusEnum.SPAM);
  }

  private static isValid(value: string): boolean {
    return Object.values(CommentStatusEnum).includes(value as CommentStatusEnum);
  }

  public getValue(): CommentStatusEnum {
    return this.value;
  }

  public isPending(): boolean {
    return this.value === CommentStatusEnum.PENDING;
  }

  public isApproved(): boolean {
    return this.value === CommentStatusEnum.APPROVED;
  }

  public isRejected(): boolean {
    return this.value === CommentStatusEnum.REJECTED;
  }

  public isSpam(): boolean {
    return this.value === CommentStatusEnum.SPAM;
  }

  public equals(other: CommentStatus): boolean {
    return this.value === other.value;
  }

  public toString(): string {
    return this.value;
  }
}
