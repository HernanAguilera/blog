import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';

export class GeneratePostPreviewUseCase {
    constructor(
        private readonly postRepository: PostRepositoryInterface
    ) {}

    async execute(id: string, token: string): Promise<{ preview_url: string }> {
        try {
            if (!id || id.trim().length === 0) {
                throw new Error('Post ID is required');
            }

            if (!token || token.trim().length === 0) {
                throw new Error('Authentication token is required');
            }

            return await this.postRepository.generatePreview(id, token);
        } catch (error) {
            throw new Error(`Failed to generate post preview: ${error instanceof Error ? error.message : 'Unknown error'}`);
        }
    }
}