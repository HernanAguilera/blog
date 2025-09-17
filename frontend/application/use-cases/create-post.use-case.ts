import type { Post } from '../../domain/entities/post.entity';
import { PostTitle } from '../../domain/value-objects/post-title.vo';
import { PostContent } from '../../domain/value-objects/post-content.vo';
import { PostStatus } from '../../domain/value-objects/post-status.vo';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { CreatePostData } from '../../domain/types/post.types';

export class CreatePostUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(data: CreatePostData, token: string): Promise<Post> {
        try {
            if (!token || token.trim().length === 0) {
                throw new Error('Authentication token is required');
            }

            // Validate input using value objects
            const title = new PostTitle(data.title);
            const content = new PostContent(data.content);

            // Validate status if provided
            if (data.status) {
                new PostStatus(data.status);
            }

            // Validate scheduled date if provided
            if (data.scheduledAt) {
                const scheduledDate = new Date(data.scheduledAt);
                if (scheduledDate <= new Date()) {
                    throw new Error('Scheduled date must be in the future');
                }
            }

            const createData: CreatePostData = {
                title: title.value(),
                content: content.value(),
                status: data.status || PostStatus.DRAFT,
                scheduledAt: data.scheduledAt
            };

            return await this.postRepository.createPost(createData, token);
        } catch (error) {
            throw new Error(`Failed to create post: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}