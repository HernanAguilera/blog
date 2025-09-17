export type PostData = {
    id: string;
    title: string;
    content: string;
    slug: string;
    status: string;
    authorId: string;
    scheduledAt?: string;
    publishedAt?: string;
    createdAt?: string;
    updatedAt?: string;
};

export type CreatePostData = {
    title: string;
    content: string;
    status?: string;
    scheduledAt?: string;
};

export type UpdatePostData = {
    title?: string;
    content?: string;
    status?: string;
    scheduledAt?: string;
};

import type {
    BaseFilter,
    DateRangeFilter,
    StatusFilter,
    AuthorFilter,
    PaginatedResponse
} from '../../shared/types/common.types';

export type PostFilters = BaseFilter & DateRangeFilter & StatusFilter & AuthorFilter;

export type PostListResponse = PaginatedResponse<PostData>;

export type PostTransitionsResponse = {
    current_status: string;
    available_transitions: string[];
    can_schedule: boolean;
    is_ready_to_publish: boolean;
    scheduled_at?: string;
    published_at?: string;
};

export type PostStatusChangeData = {
    postId: string;
    newStatus: string;
    scheduledAt?: string;
};