export class PostId {
    constructor(private readonly value_: string) {
        this.validateId(value_);
    }

    private validateId(id: string): void {
        if (!id || typeof id !== 'string') {
            throw new Error('Post ID must be a non-empty string');
        }

        if (id.trim().length === 0) {
            throw new Error('Post ID cannot be empty');
        }

        if (id.length > 50) {
            throw new Error('Post ID cannot exceed 50 characters');
        }
    }

    public value(): string {
        return this.value_;
    }

    public equals(other: PostId): boolean {
        return this.value_ === other.value_;
    }

    public toString(): string {
        return this.value_;
    }
}