export class PostSlug {
    constructor(private readonly value_: string) {
        this.validateSlug(value_);
    }

    private validateSlug(slug: string): void {
        if (typeof slug !== 'string') {
            throw new Error('Post slug must be a string');
        }

        const trimmedSlug = slug.trim();

        if (trimmedSlug.length === 0) {
            throw new Error('Post slug cannot be empty');
        }

        if (trimmedSlug.length < 3) {
            throw new Error('Post slug must be at least 3 characters long');
        }

        if (trimmedSlug.length > 255) {
            throw new Error('Post slug cannot exceed 255 characters');
        }

        // Slug format validation: lowercase, alphanumeric, hyphens only
        const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
        if (!slugPattern.test(trimmedSlug)) {
            throw new Error('Post slug must contain only lowercase letters, numbers, and hyphens (no consecutive hyphens)');
        }

        // Cannot start or end with hyphen
        if (trimmedSlug.startsWith('-') || trimmedSlug.endsWith('-')) {
            throw new Error('Post slug cannot start or end with a hyphen');
        }
    }

    public value(): string {
        return this.value_.trim();
    }

    public equals(other: PostSlug): boolean {
        return this.value_.trim() === other.value_.trim();
    }

    public toString(): string {
        return this.value();
    }

    // Static helper to generate slug from title
    public static fromTitle(title: string): PostSlug {
        const slug = title
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '') // Remove special characters except hyphens and spaces
            .replace(/[\s_-]+/g, '-') // Replace spaces, underscores with single hyphen
            .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens

        return new PostSlug(slug);
    }

    public static isValidSlug(slug: string): boolean {
        try {
            new PostSlug(slug);
            return true;
        } catch {
            return false;
        }
    }
}