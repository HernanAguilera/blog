import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { PostFilters, PostListResponse } from '../../domain/types/post.types';

export class GetPostsUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(token: string, filters?: PostFilters): Promise<PostListResponse> {
        try {
            return await this.postRepository.getPosts(token, filters);
        } catch (error) {
            throw new Error(`Failed to get posts: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}