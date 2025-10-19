/**
 * API Error
 *
 * Error de infraestructura cuando una llamada al API falla
 */

export class ApiError extends Error {
  constructor(
    message: string,
    public readonly statusCode: number,
    public readonly response?: unknown
  ) {
    super(message);
    this.name = 'ApiError';
    Object.setPrototypeOf(this, ApiError.prototype);
  }

  static fromResponse(statusCode: number, message: string, response?: unknown): ApiError {
    return new ApiError(message, statusCode, response);
  }

  static badRequest(message: string = 'Bad request'): ApiError {
    return new ApiError(message, 400);
  }

  static unauthorized(message: string = 'Unauthorized'): ApiError {
    return new ApiError(message, 401);
  }

  static forbidden(message: string = 'Forbidden'): ApiError {
    return new ApiError(message, 403);
  }

  static notFound(message: string = 'Resource not found'): ApiError {
    return new ApiError(message, 404);
  }

  static rateLimitExceeded(message: string = 'Rate limit exceeded'): ApiError {
    return new ApiError(message, 429);
  }

  static serverError(message: string = 'Internal server error'): ApiError {
    return new ApiError(message, 500);
  }
}
