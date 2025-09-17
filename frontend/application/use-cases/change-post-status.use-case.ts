import type { Post } from '../../domain/entities/post.entity';
import { PostStatus } from '../../domain/value-objects/post-status.vo';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { PostStatusChangeData } from '../../domain/types/post.types';

export class ChangePostStatusUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(data: PostStatusChangeData, token: string): Promise<Post> {
        try {
            if (!token || token.trim().length === 0) {
                throw new Error('Authentication token is required');
            }

            if (!data.postId || data.postId.trim().length === 0) {
                throw new Error('Post ID is required');
            }

            // Validate new status
            const newStatus = new PostStatus(data.newStatus);

            // Validate scheduled date if transitioning to scheduled
            if (newStatus.isScheduled() && data.scheduledAt) {
                const scheduledDate = new Date(data.scheduledAt);
                if (scheduledDate <= new Date()) {
                    throw new Error('Scheduled date must be in the future');
                }
            }

            // If transitioning to scheduled, scheduledAt is required
            if (newStatus.isScheduled() && !data.scheduledAt) {
                throw new Error('Scheduled date is required when changing status to scheduled');
            }

            const statusChangeData: PostStatusChangeData = {
                postId: data.postId,
                newStatus: newStatus.value(),
                scheduledAt: data.scheduledAt
            };

            return await this.postRepository.changePostStatus(statusChangeData, token);
        } catch (error) {
            throw new Error(`Failed to change post status: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}