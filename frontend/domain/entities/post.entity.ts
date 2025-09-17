import { PostId } from '../value-objects/post-id.vo';
import { PostTitle } from '../value-objects/post-title.vo';
import { PostContent } from '../value-objects/post-content.vo';
import { PostStatus } from '../value-objects/post-status.vo';
import { PostSlug } from '../value-objects/post-slug.vo';
import { UserId } from '../value-objects/user-id.vo';
import type { PostData } from '../types/post.types';

export class Post {
    constructor(
        private readonly id: PostId,
        private readonly title: PostTitle,
        private readonly content: PostContent,
        private readonly slug: PostSlug,
        private readonly status: PostStatus,
        private readonly authorId: UserId,
        private readonly scheduledAt?: Date,
        private readonly publishedAt?: Date,
        private readonly createdAt?: Date,
        private readonly updatedAt?: Date
    ) {}

    // Getters
    public getId(): PostId {
        return this.id;
    }

    public getTitle(): PostTitle {
        return this.title;
    }

    public getContent(): PostContent {
        return this.content;
    }

    public getSlug(): PostSlug {
        return this.slug;
    }

    public getStatus(): PostStatus {
        return this.status;
    }

    public getAuthorId(): UserId {
        return this.authorId;
    }

    public getScheduledAt(): Date | undefined {
        return this.scheduledAt;
    }

    public getPublishedAt(): Date | undefined {
        return this.publishedAt;
    }

    public getCreatedAt(): Date | undefined {
        return this.createdAt;
    }

    public getUpdatedAt(): Date | undefined {
        return this.updatedAt;
    }

    // Business methods
    public isDraft(): boolean {
        return this.status.isDraft();
    }

    public isPublished(): boolean {
        return this.status.isPublished();
    }

    public isScheduled(): boolean {
        return this.status.isScheduled();
    }

    public isArchived(): boolean {
        return this.status.isArchived();
    }

    public canBeEdited(): boolean {
        return this.status.canBeEdited();
    }

    public isPublic(): boolean {
        return this.status.isPublic();
    }

    public isReadyToPublish(): boolean {
        return !this.title.isEmpty() && !this.content.isEmpty();
    }

    public isScheduledForFuture(): boolean {
        if (!this.isScheduled() || !this.scheduledAt) {
            return false;
        }
        return this.scheduledAt > new Date();
    }

    public canBeScheduled(): boolean {
        return this.isReadyToPublish() && (this.isDraft() || this.isScheduled());
    }

    public belongsToAuthor(authorId: UserId): boolean {
        return this.authorId.equals(authorId);
    }

    // Utility methods
    public getExcerpt(maxLength: number = 150): string {
        return this.content.excerpt(maxLength);
    }

    public getWordCount(): number {
        return this.content.wordCount();
    }

    public getStatusLabel(): string {
        const status = this.status.value();
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    public getPublicUrl(): string {
        // URL format: /{year}/{month}/{slug}
        if (!this.publishedAt) {
            return '';
        }

        const year = this.publishedAt.getFullYear();
        const month = String(this.publishedAt.getMonth() + 1).padStart(2, '0');

        return `/${year}/${month}/${this.slug.value()}`;
    }

    public equals(other: Post): boolean {
        return this.id.equals(other.id);
    }

    // Serialization methods
    public toPlainObject(): PostData {
        return {
            id: this.id.value(),
            title: this.title.value(),
            content: this.content.value(),
            slug: this.slug.value(),
            status: this.status.value(),
            authorId: this.authorId.value(),
            scheduledAt: this.scheduledAt?.toISOString(),
            publishedAt: this.publishedAt?.toISOString(),
            createdAt: this.createdAt?.toISOString(),
            updatedAt: this.updatedAt?.toISOString()
        };
    }

    public toJSON(): PostData {
        return this.toPlainObject();
    }

    // Factory methods
    public static fromApiResponse(data: any): Post {
        if (!data) {
            throw new Error('Cannot create Post from null or undefined data');
        }

        return new Post(
            new PostId(String(data.id)),
            new PostTitle(data.title),
            new PostContent(data.content),
            new PostSlug(data.slug),
            new PostStatus(data.status),
            new UserId(String(data.author_id)),
            data.scheduled_at ? new Date(data.scheduled_at) : undefined,
            data.published_at ? new Date(data.published_at) : undefined,
            data.created_at ? new Date(data.created_at) : undefined,
            data.updated_at ? new Date(data.updated_at) : undefined
        );
    }

    public static create(
        title: string,
        content: string,
        authorId: string,
        slug?: string,
        status: string = PostStatus.DRAFT
    ): Post {
        const postSlug = slug ? new PostSlug(slug) : PostSlug.fromTitle(title);

        return new Post(
            new PostId(crypto.randomUUID()),
            new PostTitle(title),
            new PostContent(content),
            postSlug,
            new PostStatus(status),
            new UserId(authorId),
            undefined, // scheduledAt
            undefined, // publishedAt
            new Date(), // createdAt
            new Date()  // updatedAt
        );
    }

    public static createDraft(
        title: string,
        content: string,
        authorId: string,
        slug?: string
    ): Post {
        return Post.create(title, content, authorId, slug, PostStatus.DRAFT);
    }
}