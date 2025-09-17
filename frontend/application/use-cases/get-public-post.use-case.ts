import type { Post } from '../../domain/entities/post.entity';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';

export class GetPublicPostUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(slug: string): Promise<Post> {
        try {
            if (!slug || slug.trim().length === 0) {
                throw new Error('Post slug is required');
            }

            return await this.postRepository.getPublicPost(slug);
        } catch (error) {
            throw new Error(`Failed to get public post: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}