/**
 * Invalid Anonymous Author Error
 *
 * Error de dominio cuando los datos del autor anónimo no son válidos
 */

export class InvalidAnonymousAuthorError extends Error {
  constructor(message: string = 'Invalid anonymous author') {
    super(message);
    this.name = 'InvalidAnonymousAuthorError';
    Object.setPrototypeOf(this, InvalidAnonymousAuthorError.prototype);
  }

  static nameTooShort(minLength: number, actualLength: number): InvalidAnonymousAuthorError {
    return new InvalidAnonymousAuthorError(
      `Author name must be at least ${minLength} characters long. Got ${actualLength} characters.`
    );
  }

  static nameTooLong(maxLength: number, actualLength: number): InvalidAnonymousAuthorError {
    return new InvalidAnonymousAuthorError(
      `Author name cannot exceed ${maxLength} characters. Got ${actualLength} characters.`
    );
  }

  static nameEmpty(): InvalidAnonymousAuthorError {
    return new InvalidAnonymousAuthorError(
      'Author name cannot be empty or only whitespace'
    );
  }

  static invalidEmail(email: string): InvalidAnonymousAuthorError {
    return new InvalidAnonymousAuthorError(`Invalid email address: ${email}`);
  }

  static emailRequired(): InvalidAnonymousAuthorError {
    return new InvalidAnonymousAuthorError('Author email is required');
  }

  static nameRequired(): InvalidAnonymousAuthorError {
    return new InvalidAnonymousAuthorError('Author name is required');
  }
}
