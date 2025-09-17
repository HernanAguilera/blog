import type { Post } from '../../domain/entities/post.entity';
import type { PostFilters, PostListResponse } from '../../domain/types/post.types';
import type { LoadingState, AsyncState } from '../../shared/types/common.types';

export type PostsStoreState = {
    // Posts data
    posts: Post[];
    currentPost: Post | null;

    // Pagination
    pagination: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    } | null;

    // Loading states
    loading: LoadingState;
    saving: LoadingState;
    deleting: LoadingState;

    // Filters
    filters: PostFilters;

    // Auto-save functionality
    autoSave: {
        enabled: boolean;
        interval: number; // in milliseconds
        timer: NodeJS.Timeout | null;
        lastSaved: Date | null;
        hasUnsavedChanges: boolean;
    };

    // Error handling
    error: string | null;
    validationErrors: Record<string, string[]>;
};

export type AutoSaveConfig = {
    enabled: boolean;
    interval?: number;
};

export type PostFormData = {
    title: string;
    content: string;
    status: string;
    scheduledAt?: string;
};

export type PostUpdateData = Partial<PostFormData>;

export type PostsStoreActions = {
    // Fetching
    fetchPosts(): Promise<void>;
    fetchPost(id: string): Promise<void>;
    fetchPublicPosts(): Promise<void>;
    fetchPublicPost(slug: string): Promise<void>;

    // CRUD operations
    createPost(data: PostFormData): Promise<Post | null>;
    updatePost(id: string, data: PostUpdateData): Promise<Post | null>;
    deletePost(id: string): Promise<boolean>;

    // Status management
    changePostStatus(id: string, status: string, scheduledAt?: string): Promise<Post | null>;
    getPostTransitions(id: string): Promise<any>;

    // Auto-save
    enableAutoSave(config?: AutoSaveConfig): void;
    disableAutoSave(): void;
    saveCurrentPost(): Promise<void>;
    markAsChanged(): void;
    markAsSaved(): void;

    // Filters
    setFilter(filter: Partial<PostFilters>): void;
    clearFilters(): void;
    applyFilters(): Promise<void>;

    // Utilities
    clearError(): void;
    clearValidationErrors(): void;
    setCurrentPost(post: Post | null): void;
    reset(): void;
};