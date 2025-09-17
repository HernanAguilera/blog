import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { ApiResponse } from '../../shared/types/common.types';

export class DeletePostUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(id: string, token: string): Promise<ApiResponse> {
        try {
            if (!id || id.trim().length === 0) {
                throw new Error('Post ID is required');
            }

            if (!token || token.trim().length === 0) {
                throw new Error('Authentication token is required');
            }

            return await this.postRepository.deletePost(id, token);
        } catch (error) {
            throw new Error(`Failed to delete post: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}