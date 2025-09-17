import { Post } from '../../domain/entities/post.entity';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { HttpClientInterface } from '../services/http-client.interface';
import type {
    CreatePostData,
    UpdatePostData,
    PostFilters,
    PostListResponse,
    PostTransitionsResponse,
    PostStatusChangeData
} from '../../domain/types/post.types';
import type { ApiResponse } from '../../shared/types/common.types';

export class HttpPostRepository implements PostRepositoryInterface {
    constructor(
        private readonly httpClient: HttpClientInterface,
        private readonly publicBaseUrl: string = 'posts',
        private readonly adminBaseUrl: string = 'admin/posts'
    ) {}

    // Public endpoints
    async getPublicPosts(filters?: PostFilters): Promise<PostListResponse> {
        try {
            const params = this.buildQueryParams(filters);
            const response = await this.httpClient.get<PostListResponse>(
                `${this.publicBaseUrl}${params}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to fetch public posts: ${this.getErrorMessage(error)}`);
        }
    }

    async getPublicPost(slug: string): Promise<Post> {
        try {
            const response = await this.httpClient.get<{ data: any }>(
                `${this.publicBaseUrl}/${slug}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            return Post.fromApiResponse(response.data);
        } catch (error) {
            throw new Error(`Failed to fetch public post: ${this.getErrorMessage(error)}`);
        }
    }

    // Admin endpoints - CRUD operations
    async getPosts(token: string, filters?: PostFilters): Promise<PostListResponse> {
        try {
            const params = this.buildQueryParams(filters);
            const response = await this.httpClient.get<PostListResponse>(
                `${this.adminBaseUrl}${params}`,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to fetch posts: ${this.getErrorMessage(error)}`);
        }
    }

    async getPost(id: string, token: string): Promise<Post> {
        try {
            const response = await this.httpClient.get<{ data: any }>(
                `${this.adminBaseUrl}/${id}`,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            );

            return Post.fromApiResponse(response.data);
        } catch (error) {
            throw new Error(`Failed to fetch post: ${this.getErrorMessage(error)}`);
        }
    }

    async createPost(data: CreatePostData, token: string): Promise<Post> {
        try {
            const requestPayload = {
                title: data.title.trim(),
                content: data.content,
                status: data.status || 'draft',
                scheduled_at: data.scheduledAt
            };

            const response = await this.httpClient.post<{ data: any }>(
                this.adminBaseUrl,
                requestPayload,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            const post = Post.fromApiResponse(response.data);

            return post;
        } catch (error) {
            throw new Error(`Failed to create post: ${this.getErrorMessage(error)}`);
        }
    }

    async updatePost(id: string, data: UpdatePostData, token: string): Promise<Post> {
        try {
            const updateData: any = {};
            if (data.title !== undefined) updateData.title = data.title.trim();
            if (data.content !== undefined) updateData.content = data.content;
            if (data.status !== undefined) updateData.status = data.status;
            if (data.scheduledAt !== undefined) updateData.scheduled_at = data.scheduledAt;

            const response = await this.httpClient.put<{ data: any }>(
                `${this.adminBaseUrl}/${id}`,
                updateData,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return Post.fromApiResponse(response.data);
        } catch (error) {
            throw new Error(`Failed to update post: ${this.getErrorMessage(error)}`);
        }
    }

    async deletePost(id: string, token: string): Promise<ApiResponse> {
        try {
            const response = await this.httpClient.delete<ApiResponse>(
                `${this.adminBaseUrl}/${id}`,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to delete post: ${this.getErrorMessage(error)}`);
        }
    }

    // Admin endpoints - Status management
    async changePostStatus(data: PostStatusChangeData, token: string): Promise<Post> {
        try {
            const response = await this.httpClient.patch<{ data: any }>(
                `${this.adminBaseUrl}/${data.postId}/transition`,
                {
                    status: data.newStatus,
                    scheduled_at: data.scheduledAt
                },
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return Post.fromApiResponse(response.data);
        } catch (error) {
            throw new Error(`Failed to change post status: ${this.getErrorMessage(error)}`);
        }
    }

    async getPostTransitions(id: string, token: string): Promise<PostTransitionsResponse> {
        try {
            const response = await this.httpClient.get<PostTransitionsResponse>(
                `${this.adminBaseUrl}/${id}/transitions`,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to fetch post transitions: ${this.getErrorMessage(error)}`);
        }
    }

    // Admin endpoints - Preview and draft management
    async saveAsDraft(id: string, data: UpdatePostData, token: string): Promise<Post> {
        try {
            const updateData = {
                ...data,
                status: 'draft'
            };

            return await this.updatePost(id, updateData, token);
        } catch (error) {
            throw new Error(`Failed to save as draft: ${this.getErrorMessage(error)}`);
        }
    }

    async generatePreview(id: string, token: string): Promise<{ preview_url: string }> {
        try {
            const response = await this.httpClient.post<{ preview_url: string }>(
                `${this.adminBaseUrl}/${id}/preview`,
                {},
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to generate preview: ${this.getErrorMessage(error)}`);
        }
    }

    // Private helper methods
    private buildQueryParams(filters?: PostFilters): string {
        if (!filters) return '';

        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.status) params.append('status', filters.status);
        if (filters.authorId) params.append('author_id', filters.authorId);
        if (filters.dateFrom) params.append('date_from', filters.dateFrom);
        if (filters.dateTo) params.append('date_to', filters.dateTo);
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.sortBy) params.append('sort_by', filters.sortBy);
        if (filters.sortDirection) params.append('sort_direction', filters.sortDirection);

        const queryString = params.toString();
        return queryString ? `?${queryString}` : '';
    }

    private getErrorMessage(error: any): string {
        if (error?.message) {
            return error.message;
        }

        if (error?.response?.data?.message) {
            return error.response.data.message;
        }

        if (typeof error === 'string') {
            return error;
        }

        return 'An unexpected error occurred';
    }
}