export class PageTitle {
  private constructor(private readonly value: string) {
    this.validate();
  }

  static fromString(title: string): PageTitle {
    return new PageTitle(title.trim());
  }

  private validate(): void {
    if (!this.value || this.value.trim() === '') {
      throw new Error('PageTitle cannot be empty');
    }

    if (this.value.length > 255) {
      throw new Error('PageTitle cannot exceed 255 characters');
    }
  }

  getValue(): string {
    return this.value;
  }

  equals(other: PageTitle): boolean {
    return this.value === other.value;
  }

  toString(): string {
    return this.value;
  }
}
