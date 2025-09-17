export class PostTitle {
    constructor(private readonly value_: string) {
        this.validateTitle(value_);
    }

    private validateTitle(title: string): void {
        if (typeof title !== 'string') {
            throw new Error('Post title must be a string');
        }

        const trimmedTitle = title.trim();

        if (trimmedTitle.length === 0) {
            throw new Error('Post title cannot be empty');
        }

        if (trimmedTitle.length < 3) {
            throw new Error('Post title must be at least 3 characters long');
        }

        if (trimmedTitle.length > 255) {
            throw new Error('Post title cannot exceed 255 characters');
        }
    }

    public value(): string {
        return this.value_.trim();
    }

    public equals(other: PostTitle): boolean {
        return this.value_.trim() === other.value_.trim();
    }

    public toString(): string {
        return this.value();
    }

    public isEmpty(): boolean {
        return this.value_.trim().length === 0;
    }

    public length(): number {
        return this.value_.trim().length;
    }
}