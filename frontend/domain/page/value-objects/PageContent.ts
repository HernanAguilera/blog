export class PageContent {
  private constructor(private readonly value: string) {}

  static fromString(content: string): PageContent {
    return new PageContent(content);
  }

  static empty(): PageContent {
    return new PageContent('');
  }

  getValue(): string {
    return this.value;
  }

  isEmpty(): boolean {
    return this.value.trim() === '';
  }

  equals(other: PageContent): boolean {
    return this.value === other.value;
  }

  toString(): string {
    return this.value;
  }
}
