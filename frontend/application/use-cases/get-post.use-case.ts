import type { Post } from '../../domain/entities/post.entity';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';

export class GetPostUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(id: string, token: string): Promise<Post> {
        try {
            if (!id || id.trim().length === 0) {
                throw new Error('Post ID is required');
            }

            if (!token || token.trim().length === 0) {
                throw new Error('Authentication token is required');
            }

            return await this.postRepository.getPost(id, token);
        } catch (error) {
            throw new Error(`Failed to get post: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}