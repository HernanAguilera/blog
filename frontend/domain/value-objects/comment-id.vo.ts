/**
 * CommentId Value Object
 *
 * Representa el identificador único de un comentario (UUID)
 */

import { InvalidCommentIdError } from '../exceptions/comment';

export class CommentId {
  private readonly value: string;

  private constructor(value: string) {
    this.value = value;
  }

  public static create(value: string): CommentId {
    CommentId.validate(value);
    return new CommentId(value);
  }

  private static validate(value: string): void {
    if (!value || typeof value !== 'string' || value.trim().length === 0) {
      throw InvalidCommentIdError.empty();
    }

    // UUID v4 regex pattern
    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;

    if (!uuidRegex.test(value)) {
      throw InvalidCommentIdError.invalidFormat(value);
    }
  }

  public static isValid(value: string): boolean {
    if (!value || typeof value !== 'string') {
      return false;
    }

    // UUID v4 regex pattern
    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
    return uuidRegex.test(value);
  }

  public getValue(): string {
    return this.value;
  }

  public equals(other: CommentId): boolean {
    return this.value === other.value;
  }

  public toString(): string {
    return this.value;
  }
}
