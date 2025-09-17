import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { PostFilters, PostListResponse } from '../../domain/types/post.types';

export class GetPublicPostsUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(filters?: PostFilters): Promise<PostListResponse> {
        try {
            return await this.postRepository.getPublicPosts(filters);
        } catch (error) {
            throw new Error(`Failed to get public posts: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}