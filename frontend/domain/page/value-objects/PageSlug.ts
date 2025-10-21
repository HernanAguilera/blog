export class PageSlug {
  private constructor(private readonly value: string) {
    this.validate();
  }

  static fromString(slug: string): PageSlug {
    return new PageSlug(slug);
  }

  private validate(): void {
    if (!this.value || this.value.trim() === '') {
      throw new Error('PageSlug cannot be empty');
    }

    if (this.value.length > 100) {
      throw new Error('PageSlug cannot exceed 100 characters');
    }

    if (!/^[a-z0-9\-]+$/.test(this.value)) {
      throw new Error('PageSlug can only contain lowercase letters, numbers, and hyphens');
    }
  }

  getValue(): string {
    return this.value;
  }

  equals(other: PageSlug): boolean {
    return this.value === other.value;
  }

  toString(): string {
    return this.value;
  }
}
