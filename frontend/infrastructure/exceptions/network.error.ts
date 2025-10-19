/**
 * Network Error
 *
 * Error de infraestructura cuando falla la conexión de red
 */

export class NetworkError extends Error {
  constructor(message: string = 'Network error') {
    super(message);
    this.name = 'NetworkError';
    Object.setPrototypeOf(this, NetworkError.prototype);
  }

  static connectionFailed(): NetworkError {
    return new NetworkError('Failed to connect to server');
  }

  static timeout(): NetworkError {
    return new NetworkError('Request timeout');
  }

  static offline(): NetworkError {
    return new NetworkError('No internet connection');
  }
}
