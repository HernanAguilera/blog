/**
 * Invalid Comment ID Error
 *
 * Error de dominio cuando el ID del comentario no es válido
 */

export class InvalidCommentIdError extends Error {
  constructor(message: string = 'Invalid comment ID') {
    super(message);
    this.name = 'InvalidCommentIdError';
    Object.setPrototypeOf(this, InvalidCommentIdError.prototype);
  }

  static invalidFormat(value: string): InvalidCommentIdError {
    return new InvalidCommentIdError(
      `Invalid comment ID: ${value}. Must be a valid UUID.`
    );
  }

  static empty(): InvalidCommentIdError {
    return new InvalidCommentIdError('Comment ID cannot be empty');
  }
}
