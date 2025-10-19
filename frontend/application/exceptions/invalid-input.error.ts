/**
 * Invalid Input Error
 *
 * Error de aplicación cuando los datos de entrada no son válidos
 */

export class InvalidInputError extends Error {
  constructor(message: string = 'Invalid input') {
    super(message);
    this.name = 'InvalidInputError';
    Object.setPrototypeOf(this, InvalidInputError.prototype);
  }

  static postSlugRequired(): InvalidInputError {
    return new InvalidInputError('Post slug is required');
  }

  static turnstileTokenRequired(): InvalidInputError {
    return new InvalidInputError('Turnstile token is required');
  }

  static emptyBulkOperation(): InvalidInputError {
    return new InvalidInputError('At least one comment ID is required');
  }
}
