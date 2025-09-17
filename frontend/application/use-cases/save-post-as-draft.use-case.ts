import type { Post } from '../../domain/entities/post.entity';
import { PostTitle } from '../../domain/value-objects/post-title.vo';
import { PostContent } from '../../domain/value-objects/post-content.vo';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { UpdatePostData } from '../../domain/types/post.types';

export class SavePostAsDraftUseCase {
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
            const draftData: UpdatePostData = {};

            if (data.title !== undefined) {
                const title = new PostTitle(data.title);
                draftData.title = title.value();
            }

            if (data.content !== undefined) {
                const content = new PostContent(data.content);
                draftData.content = content.value();
            }

            // Remove scheduled date when saving as draft
            draftData.scheduledAt = undefined;

            return await this.postRepository.saveAsDraft(id, draftData, token);
        } catch (error) {
            throw new Error(`Failed to save post as draft: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}