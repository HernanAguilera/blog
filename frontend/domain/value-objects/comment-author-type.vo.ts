/**
 * CommentAuthorType Value Object
 *
 * Representa el tipo de autor de un comentario
 */

import { CommentAuthorType as CommentAuthorTypeEnum } from '../types/comment.types';

export class CommentAuthorType {
  private readonly value: CommentAuthorTypeEnum;

  private constructor(value: CommentAuthorTypeEnum) {
    this.value = value;
  }

  public static create(value: string): CommentAuthorType {
    if (!CommentAuthorType.isValid(value)) {
      throw new Error(
        `Invalid comment author type: ${value}. Valid values are: ${Object.values(CommentAuthorTypeEnum).join(', ')}`
      );
    }

    return new CommentAuthorType(value as CommentAuthorTypeEnum);
  }

  public static createUser(): CommentAuthorType {
    return new CommentAuthorType(CommentAuthorTypeEnum.USER);
  }

  public static createAnonymous(): CommentAuthorType {
    return new CommentAuthorType(CommentAuthorTypeEnum.ANONYMOUS);
  }

  private static isValid(value: string): boolean {
    return Object.values(CommentAuthorTypeEnum).includes(value as CommentAuthorTypeEnum);
  }

  public getValue(): CommentAuthorTypeEnum {
    return this.value;
  }

  public isUser(): boolean {
    return this.value === CommentAuthorTypeEnum.USER;
  }

  public isAnonymous(): boolean {
    return this.value === CommentAuthorTypeEnum.ANONYMOUS;
  }

  public equals(other: CommentAuthorType): boolean {
    return this.value === other.value;
  }

  public toString(): string {
    return this.value;
  }
}
