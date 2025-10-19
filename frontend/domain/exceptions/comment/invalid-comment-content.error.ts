/**
 * Invalid Comment Content Error
 *
 * Error de dominio cuando el contenido del comentario no es válido
 */

export class InvalidCommentContentError extends Error {
  constructor(message: string = 'Invalid comment content') {
    super(message);
    this.name = 'InvalidCommentContentError';
    Object.setPrototypeOf(this, InvalidCommentContentError.prototype);
  }

  static tooShort(minLength: number, actualLength: number): InvalidCommentContentError {
    return new InvalidCommentContentError(
      `Comment content must be at least ${minLength} characters long. Got ${actualLength} characters.`
    );
  }

  static tooLong(maxLength: number, actualLength: number): InvalidCommentContentError {
    return new InvalidCommentContentError(
      `Comment content cannot exceed ${maxLength} characters. Got ${actualLength} characters.`
    );
  }

  static empty(): InvalidCommentContentError {
    return new InvalidCommentContentError(
      'Comment content cannot be empty or only whitespace'
    );
  }
}
