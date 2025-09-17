export class PostStatus {
    public static readonly DRAFT = 'draft';
    public static readonly SCHEDULED = 'scheduled';
    public static readonly PUBLISHED = 'published';
    public static readonly ARCHIVED = 'archived';

    private static readonly VALID_STATUSES = [
        PostStatus.DRAFT,
        PostStatus.SCHEDULED,
        PostStatus.PUBLISHED,
        PostStatus.ARCHIVED
    ] as const;

    constructor(private readonly value_: string) {
        this.validateStatus(value_);
    }

    private validateStatus(status: string): void {
        if (typeof status !== 'string') {
            throw new Error('Post status must be a string');
        }

        if (!PostStatus.VALID_STATUSES.includes(status as any)) {
            throw new Error(
                `Invalid post status: ${status}. Valid statuses are: ${PostStatus.VALID_STATUSES.join(', ')}`
            );
        }
    }

    public value(): string {
        return this.value_;
    }

    public equals(other: PostStatus): boolean {
        return this.value_ === other.value_;
    }

    public toString(): string {
        return this.value_;
    }

    // Status check methods
    public isDraft(): boolean {
        return this.value_ === PostStatus.DRAFT;
    }

    public isScheduled(): boolean {
        return this.value_ === PostStatus.SCHEDULED;
    }

    public isPublished(): boolean {
        return this.value_ === PostStatus.PUBLISHED;
    }

    public isArchived(): boolean {
        return this.value_ === PostStatus.ARCHIVED;
    }

    public canBeEdited(): boolean {
        return this.isDraft() || this.isScheduled();
    }

    public isPublic(): boolean {
        return this.isPublished();
    }

    // Static factory methods
    public static draft(): PostStatus {
        return new PostStatus(PostStatus.DRAFT);
    }

    public static scheduled(): PostStatus {
        return new PostStatus(PostStatus.SCHEDULED);
    }

    public static published(): PostStatus {
        return new PostStatus(PostStatus.PUBLISHED);
    }

    public static archived(): PostStatus {
        return new PostStatus(PostStatus.ARCHIVED);
    }

    public static getValidStatuses(): readonly string[] {
        return PostStatus.VALID_STATUSES;
    }
}