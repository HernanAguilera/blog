/**
 * AnonymousAuthor Value Object
 *
 * Representa un autor anónimo con validaciones de nombre y email
 */

import { InvalidAnonymousAuthorError } from '../exceptions/comment';

export class AnonymousAuthor {
  private static readonly MIN_NAME_LENGTH = 2;
  private static readonly MAX_NAME_LENGTH = 100;

  private constructor(
    private readonly name: string,
    private readonly email: string
  ) {}

  public static create(name: string, email: string): AnonymousAuthor {
    AnonymousAuthor.validateName(name);
    AnonymousAuthor.validateEmail(email);

    return new AnonymousAuthor(name, email);
  }

  private static validateName(name: string): void {
    if (!name || typeof name !== 'string') {
      throw InvalidAnonymousAuthorError.nameRequired();
    }

    const trimmedName = name.trim();
    if (trimmedName.length === 0) {
      throw InvalidAnonymousAuthorError.nameEmpty();
    }

    const length = trimmedName.length;

    if (length < AnonymousAuthor.MIN_NAME_LENGTH) {
      throw InvalidAnonymousAuthorError.nameTooShort(
        AnonymousAuthor.MIN_NAME_LENGTH,
        length
      );
    }

    if (length > AnonymousAuthor.MAX_NAME_LENGTH) {
      throw InvalidAnonymousAuthorError.nameTooLong(
        AnonymousAuthor.MAX_NAME_LENGTH,
        length
      );
    }
  }

  private static validateEmail(email: string): void {
    if (!email || typeof email !== 'string') {
      throw InvalidAnonymousAuthorError.emailRequired();
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      throw InvalidAnonymousAuthorError.invalidEmail(email);
    }
  }

  public getName(): string {
    return this.name;
  }

  public getEmail(): string {
    return this.email;
  }

  public equals(other: AnonymousAuthor): boolean {
    return this.name === other.name && this.email === other.email;
  }

  public toData(): { name: string; email: string } {
    return {
      name: this.name,
      email: this.email,
    };
  }
}
