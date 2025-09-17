import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { Post } from '../../domain/entities/post.entity';
import { useAuthStore } from './auth.store';

// Use cases
import type { GetPostsUseCase } from '../../application/use-cases/get-posts.use-case';
import type { GetPostUseCase } from '../../application/use-cases/get-post.use-case';
import type { GetPublicPostsUseCase } from '../../application/use-cases/get-public-posts.use-case';
import type { GetPublicPostUseCase } from '../../application/use-cases/get-public-post.use-case';
import type { CreatePostUseCase } from '../../application/use-cases/create-post.use-case';
import type { UpdatePostUseCase } from '../../application/use-cases/update-post.use-case';
import type { DeletePostUseCase } from '../../application/use-cases/delete-post.use-case';
import type { ChangePostStatusUseCase } from '../../application/use-cases/change-post-status.use-case';
import type { GetPostTransitionsUseCase } from '../../application/use-cases/get-post-transitions.use-case';
import type { SavePostAsDraftUseCase } from '../../application/use-cases/save-post-as-draft.use-case';

import type { PostFilters, PostTransitionsResponse } from '../../domain/types/post.types';
import type { LoadingState } from '../../shared/types/common.types';
import type { PostFormData, PostUpdateData, AutoSaveConfig } from '../types/post-store.types';

export const usePostsStore = defineStore('posts', () => {
    // Dependencies
    const authStore = useAuthStore();
    const nuxtApp = useNuxtApp();

    // Use cases - injected via container (safe for SSR)
    const getPostsUseCase = nuxtApp.$container?.get<GetPostsUseCase>('GetPostsUseCase');
    const getPostUseCase = nuxtApp.$container?.get<GetPostUseCase>('GetPostUseCase');
    const getPublicPostsUseCase = nuxtApp.$container?.get<GetPublicPostsUseCase>('GetPublicPostsUseCase');
    const getPublicPostUseCase = nuxtApp.$container?.get<GetPublicPostUseCase>('GetPublicPostUseCase');
    const createPostUseCase = nuxtApp.$container?.get<CreatePostUseCase>('CreatePostUseCase');
    const updatePostUseCase = nuxtApp.$container?.get<UpdatePostUseCase>('UpdatePostUseCase');
    const deletePostUseCase = nuxtApp.$container?.get<DeletePostUseCase>('DeletePostUseCase');
    const changePostStatusUseCase = nuxtApp.$container?.get<ChangePostStatusUseCase>('ChangePostStatusUseCase');
    const getPostTransitionsUseCase = nuxtApp.$container?.get<GetPostTransitionsUseCase>('GetPostTransitionsUseCase');
    const savePostAsDraftUseCase = nuxtApp.$container?.get<SavePostAsDraftUseCase>('SavePostAsDraftUseCase');

    // State
    const posts = ref<Post[]>([]);
    const currentPost = ref<Post | null>(null);
    const pagination = ref<{
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    } | null>(null);

    // Loading states
    const loading = ref<LoadingState>('idle');
    const saving = ref<LoadingState>('idle');
    const deleting = ref<LoadingState>('idle');

    // Filters
    const filters = ref<PostFilters>({});

    // Auto-save
    const autoSaveEnabled = ref(false);
    const autoSaveInterval = ref(30000); // 30 seconds
    const autoSaveTimer = ref<NodeJS.Timeout | null>(null);
    const lastSaved = ref<Date | null>(null);
    const hasUnsavedChanges = ref(false);

    // Error handling
    const error = ref<string | null>(null);
    const validationErrors = ref<Record<string, string[]>>({});

    // Getters
    const isLoading = computed(() => loading.value === 'loading');
    const isSaving = computed(() => saving.value === 'loading');
    const isDeleting = computed(() => deleting.value === 'loading');
    const isAutoSaving = computed(() => autoSaveEnabled.value && hasUnsavedChanges.value);

    const draftPosts = computed(() => posts.value.filter(post => post.isDraft()));
    const publishedPosts = computed(() => posts.value.filter(post => post.isPublished()));
    const scheduledPosts = computed(() => posts.value.filter(post => post.isScheduled()));
    const archivedPosts = computed(() => posts.value.filter(post => post.isArchived()));

    const hasError = computed(() => !!error.value);
    const hasValidationErrors = computed(() => Object.keys(validationErrors.value).length > 0);

    // Actions
    const clearError = () => {
        error.value = null;
    };

    const clearValidationErrors = () => {
        validationErrors.value = {};
    };

    const setError = (message: string) => {
        error.value = message;
    };

    const setValidationErrors = (errors: Record<string, string[]>) => {
        validationErrors.value = errors;
    };

    // Fetching actions
    const fetchPosts = async () => {
        try {
            loading.value = 'loading';
            clearError();

            if (!getPostsUseCase) {
                throw new Error('PostsUseCase not available');
            }

            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            const response = await getPostsUseCase.execute(token, filters.value);

            posts.value = response.data.map((postData: any): Post => Post.fromApiResponse(postData));
            pagination.value = response.pagination;

            loading.value = 'success';
        } catch (err) {
            loading.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to fetch posts');
            throw err;
        }
    };

    const fetchPost = async (id: string) => {
        try {
            loading.value = 'loading';
            clearError();

            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            const post = await getPostUseCase.execute(id, token);
            currentPost.value = post;

            loading.value = 'success';
            return post;
        } catch (err) {
            loading.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to fetch post');
            throw err;
        }
    };

    const fetchPublicPosts = async () => {
        try {
            loading.value = 'loading';
            clearError();

            const response = await getPublicPostsUseCase.execute(filters.value);

            posts.value = response.data.map((postData: any): Post => Post.fromApiResponse(postData));
            pagination.value = response.pagination;

            loading.value = 'success';
        } catch (err) {
            loading.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to fetch public posts');
            throw err;
        }
    };

    const fetchPublicPost = async (slug: string) => {
        try {
            loading.value = 'loading';
            clearError();

            const post = await getPublicPostUseCase.execute(slug);
            currentPost.value = post;

            loading.value = 'success';
            return post;
        } catch (err) {
            loading.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to fetch public post');
            throw err;
        }
    };

    // CRUD actions
    const createPost = async (data: PostFormData): Promise<Post | null> => {
        try {
            saving.value = 'loading';
            clearError();
            clearValidationErrors();

            if (!createPostUseCase) {
                throw new Error('CreatePostUseCase not available');
            }

            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            const post = await createPostUseCase.execute(data, token);

            // Add to posts array
            posts.value.unshift(post);
            currentPost.value = post;

            saving.value = 'success';
            return post;
        } catch (err) {
            saving.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to create post');
            return null;
        }
    };

    const updatePost = async (id: string, data: PostUpdateData): Promise<Post | null> => {
        try {
            saving.value = 'loading';
            clearError();
            clearValidationErrors();

            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            const updatedPost = await updatePostUseCase.execute(id, data, token);

            // Update in posts array
            const index = posts.value.findIndex(post => post.getId().value() === id);
            if (index !== -1) {
                posts.value[index] = updatedPost;
            }

            // Update current post if it's the same
            if (currentPost.value?.getId().value() === id) {
                currentPost.value = updatedPost;
            }

            saving.value = 'success';
            markAsSaved();
            return updatedPost;
        } catch (err) {
            saving.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to update post');
            return null;
        }
    };

    const deletePost = async (id: string): Promise<boolean> => {
        try {
            deleting.value = 'loading';
            clearError();

            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            await deletePostUseCase.execute(id, token);

            // Remove from posts array
            posts.value = posts.value.filter(post => post.getId().value() !== id);

            // Clear current post if it's the deleted one
            if (currentPost.value?.getId().value() === id) {
                currentPost.value = null;
            }

            deleting.value = 'success';
            return true;
        } catch (err) {
            deleting.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to delete post');
            return false;
        }
    };

    // Status management
    const changePostStatus = async (id: string, status: string, scheduledAt?: string): Promise<Post | null> => {
        try {
            saving.value = 'loading';
            clearError();

            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            const updatedPost = await changePostStatusUseCase.execute({
                postId: id,
                newStatus: status,
                scheduledAt
            }, token);

            // Update in posts array
            const index = posts.value.findIndex(post => post.getId().value() === id);
            if (index !== -1) {
                posts.value[index] = updatedPost;
            }

            // Update current post if it's the same
            if (currentPost.value?.getId().value() === id) {
                currentPost.value = updatedPost;
            }

            saving.value = 'success';
            return updatedPost;
        } catch (err) {
            saving.value = 'error';
            setError(err instanceof Error ? err.message : 'Failed to change post status');
            return null;
        }
    };

    const getPostTransitions = async (id: string): Promise<PostTransitionsResponse | null> => {
        try {
            clearError();

            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            return await getPostTransitionsUseCase.execute(id, token);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to get post transitions');
            return null;
        }
    };

    // Auto-save functionality
    const enableAutoSave = (config: AutoSaveConfig = { enabled: true }) => {
        autoSaveEnabled.value = config.enabled;
        if (config.interval) {
            autoSaveInterval.value = config.interval;
        }

        if (autoSaveEnabled.value && currentPost.value) {
            startAutoSaveTimer();
        }
    };

    const disableAutoSave = () => {
        autoSaveEnabled.value = false;
        stopAutoSaveTimer();
    };

    const startAutoSaveTimer = () => {
        stopAutoSaveTimer();

        autoSaveTimer.value = setInterval(() => {
            if (hasUnsavedChanges.value && currentPost.value) {
                saveCurrentPost();
            }
        }, autoSaveInterval.value);
    };

    const stopAutoSaveTimer = () => {
        if (autoSaveTimer.value) {
            clearInterval(autoSaveTimer.value);
            autoSaveTimer.value = null;
        }
    };

    const saveCurrentPost = async () => {
        if (!currentPost.value || !hasUnsavedChanges.value) {
            return;
        }

        try {
            const token = authStore.token;
            if (!token) {
                throw new Error('Authentication required');
            }

            const id = currentPost.value.getId().value();
            const data = {
                title: currentPost.value.getTitle().value(),
                content: currentPost.value.getContent().value()
            };

            await savePostAsDraftUseCase.execute(id, data, token);
            markAsSaved();
        } catch (err) {
            console.error('Auto-save failed:', err);
        }
    };

    const markAsChanged = () => {
        hasUnsavedChanges.value = true;
    };

    const markAsSaved = () => {
        hasUnsavedChanges.value = false;
        lastSaved.value = new Date();
    };

    // Filter management
    const setFilter = (filter: Partial<PostFilters>) => {
        filters.value = { ...filters.value, ...filter };
    };

    const clearFilters = () => {
        filters.value = {};
    };

    const applyFilters = async () => {
        await fetchPosts();
    };

    // Utilities
    const setCurrentPost = (post: Post | null) => {
        // Stop auto-save for previous post
        stopAutoSaveTimer();

        currentPost.value = post;
        hasUnsavedChanges.value = false;

        // Start auto-save for new post if enabled
        if (autoSaveEnabled.value && post) {
            startAutoSaveTimer();
        }
    };

    const reset = () => {
        posts.value = [];
        currentPost.value = null;
        pagination.value = null;
        loading.value = 'idle';
        saving.value = 'idle';
        deleting.value = 'idle';
        filters.value = {};
        error.value = null;
        validationErrors.value = {};

        // Reset auto-save
        disableAutoSave();
        hasUnsavedChanges.value = false;
        lastSaved.value = null;
    };

    // Cleanup on unmount
    const cleanup = () => {
        stopAutoSaveTimer();
    };

    return {
        // State
        posts,
        currentPost,
        pagination,
        filters,
        error,
        validationErrors,
        lastSaved,

        // Computed
        isLoading,
        isSaving,
        isDeleting,
        isAutoSaving,
        hasUnsavedChanges,
        draftPosts,
        publishedPosts,
        scheduledPosts,
        archivedPosts,
        hasError,
        hasValidationErrors,

        // Actions
        fetchPosts,
        fetchPost,
        fetchPublicPosts,
        fetchPublicPost,
        createPost,
        updatePost,
        deletePost,
        changePostStatus,
        getPostTransitions,
        enableAutoSave,
        disableAutoSave,
        saveCurrentPost,
        markAsChanged,
        markAsSaved,
        setFilter,
        clearFilters,
        applyFilters,
        setCurrentPost,
        clearError,
        clearValidationErrors,
        reset,
        cleanup
    };
});