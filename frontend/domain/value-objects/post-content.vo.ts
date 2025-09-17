export class PostContent {
    constructor(private readonly value_: string) {
        this.validateContent(value_);
    }

    private validateContent(content: string): void {
        if (typeof content !== 'string') {
            throw new Error('Post content must be a string');
        }

        if (content.length > 1000000) {
            throw new Error('Post content cannot exceed 1MB (1,000,000 characters)');
        }
    }

    public value(): string {
        return this.value_;
    }

    public equals(other: PostContent): boolean {
        return this.value_ === other.value_;
    }

    public toString(): string {
        return this.value_;
    }

    public isEmpty(): boolean {
        return this.value_.trim().length === 0;
    }

    public length(): number {
        return this.value_.length;
    }

    public trimmedLength(): number {
        return this.value_.trim().length;
    }

    public excerpt(maxLength: number = 150): string {
        const plainText = this.value_
            .replace(/<[^>]*>/g, '') // Remove HTML tags
            .trim();

        if (plainText.length <= maxLength) {
            return plainText;
        }

        return plainText.substring(0, maxLength).trim() + '...';
    }

    public wordCount(): number {
        const plainText = this.value_
            .replace(/<[^>]*>/g, '') // Remove HTML tags
            .trim();

        if (plainText.length === 0) {
            return 0;
        }

        return plainText.split(/\s+/).length;
    }
}