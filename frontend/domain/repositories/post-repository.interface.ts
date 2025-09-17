import type { Post } from '../entities/post.entity';
import type {
    CreatePostData,
    UpdatePostData,
    PostFilters,
    PostListResponse,
    PostTransitionsResponse,
    PostStatusChangeData
} from '../types/post.types';
import type { ApiResponse } from '../../shared/types/common.types';

export interface PostRepositoryInterface {
    // Public endpoints
    getPublicPosts(filters?: PostFilters): Promise<PostListResponse>;
    getPublicPost(slug: string): Promise<Post>;

    // Admin endpoints - CRUD operations
    getPosts(token: string, filters?: PostFilters): Promise<PostListResponse>;
    getPost(id: string, token: string): Promise<Post>;
    createPost(data: CreatePostData, token: string): Promise<Post>;
    updatePost(id: string, data: UpdatePostData, token: string): Promise<Post>;
    deletePost(id: string, token: string): Promise<ApiResponse>;

    // Admin endpoints - Status management
    changePostStatus(data: PostStatusChangeData, token: string): Promise<Post>;
    getPostTransitions(id: string, token: string): Promise<PostTransitionsResponse>;

    // Admin endpoints - Preview and draft management
    saveAsDraft(id: string, data: UpdatePostData, token: string): Promise<Post>;
    generatePreview(id: string, token: string): Promise<{ preview_url: string }>;
}