export class PageId {
  private constructor(private readonly value: string) {
    this.validate();
  }

  static fromString(id: string): PageId {
    return new PageId(id);
  }

  private validate(): void {
    if (!this.value || this.value.trim() === '') {
      throw new Error('PageId cannot be empty');
    }
  }

  getValue(): string {
    return this.value;
  }

  equals(other: PageId): boolean {
    return this.value === other.value;
  }

  toString(): string {
    return this.value;
  }
}
