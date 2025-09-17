import type { Post } from '../../domain/entities/post.entity';
import { PostTitle } from '../../domain/value-objects/post-title.vo';
import { PostContent } from '../../domain/value-objects/post-content.vo';
import { PostStatus } from '../../domain/value-objects/post-status.vo';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { UpdatePostData } from '../../domain/types/post.types';

export class UpdatePostUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(id: string, data: UpdatePostData, token: string): Promise<Post> {
        try {
            if (!id || id.trim().length === 0) {
                throw new Error('Post ID is required');
            }

            if (!token || token.trim().length === 0) {
                throw new Error('Authentication token is required');
            }

            // Validate input using value objects if provided
            const updateData: UpdatePostData = {};

            if (data.title !== undefined) {
                const title = new PostTitle(data.title);
                updateData.title = title.value();
            }

            if (data.content !== undefined) {
                const content = new PostContent(data.content);
                updateData.content = content.value();
            }

            if (data.status !== undefined) {
                const status = new PostStatus(data.status);
                updateData.status = status.value();
            }

            if (data.scheduledAt !== undefined) {
                if (data.scheduledAt) {
                    const scheduledDate = new Date(data.scheduledAt);
                    if (scheduledDate <= new Date()) {
                        throw new Error('Scheduled date must be in the future');
                    }
                }
                updateData.scheduledAt = data.scheduledAt;
            }

            // Ensure at least one field is being updated
            if (Object.keys(updateData).length === 0) {
                throw new Error('At least one field must be provided for update');
            }

            return await this.postRepository.updatePost(id, updateData, token);
        } catch (error) {
            throw new Error(`Failed to update post: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}