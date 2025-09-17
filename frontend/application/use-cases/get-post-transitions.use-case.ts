import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { PostTransitionsResponse } from '../../domain/types/post.types';

export class GetPostTransitionsUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(id: string, token: string): Promise<PostTransitionsResponse> {
        try {
            if (!id || id.trim().length === 0) {
                throw new Error('Post ID is required');
            }

            if (!token || token.trim().length === 0) {
                throw new Error('Authentication token is required');
            }

            return await this.postRepository.getPostTransitions(id, token);
        } catch (error) {
            throw new Error(`Failed to get post transitions: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}