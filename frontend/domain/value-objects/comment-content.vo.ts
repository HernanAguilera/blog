/**
 * CommentContent Value Object
 *
 * Representa el contenido de un comentario con validaciones
 */

import { InvalidCommentContentError } from '../exceptions/comment';

export class CommentContent {
  private readonly value: string;

  private static readonly MIN_LENGTH = 3;
  private static readonly MAX_LENGTH = 2000;

  private constructor(value: string) {
    this.value = value;
  }

  public static create(value: string): CommentContent {
    const sanitized = CommentContent.sanitize(value);
    CommentContent.validate(sanitized);
    return new CommentContent(sanitized);
  }

  private static sanitize(value: string): string {
    if (!value || typeof value !== 'string') {
      return '';
    }

    // Trim whitespace
    return value.trim();
  }

  private static validate(value: string): void {
    if (!value || value.length === 0) {
      throw InvalidCommentContentError.empty();
    }

    const length = value.length;

    if (length < CommentContent.MIN_LENGTH) {
      throw InvalidCommentContentError.tooShort(CommentContent.MIN_LENGTH, length);
    }

    if (length > CommentContent.MAX_LENGTH) {
      throw InvalidCommentContentError.tooLong(CommentContent.MAX_LENGTH, length);
    }
  }

  public getValue(): string {
    return this.value;
  }

  public getLength(): number {
    return this.value.length;
  }

  public equals(other: CommentContent): boolean {
    return this.value === other.value;
  }

  public toString(): string {
    return this.value;
  }
}
