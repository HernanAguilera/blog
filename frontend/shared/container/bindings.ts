import type { ContainerInterface } from '../contracts/container';

// Infrastructure imports
import { LocalTokenStorage } from '../../infrastructure/storage/local-token.storage';
import { HttpClientService } from '../../infrastructure/services/http-client.service';
import { HttpUserRepository } from '../../infrastructure/repositories/http-user.repository';
import { HttpPostRepository } from '../../infrastructure/repositories/http-post.repository';

// Infrastructure types
import type { TokenStorageInterface } from '../../infrastructure/storage/token-storage.interface';
import type { HttpClientInterface } from '../../infrastructure/services/http-client.interface';
import type { UserRepositoryInterface } from '../../domain/repositories/user-repository.interface';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';

// Application imports
import { AuthorizationService } from '../../application/services/authorization.service';
import { LoginUserUseCase } from '../../application/use-cases/login-user.use-case';
import { RegisterUserUseCase } from '../../application/use-cases/register-user.use-case';
import { LogoutUserUseCase } from '../../application/use-cases/logout-user.use-case';

// Post use cases
import { GetPostsUseCase } from '../../application/use-cases/get-posts.use-case';
import { GetPostUseCase } from '../../application/use-cases/get-post.use-case';
import { GetPublicPostsUseCase } from '../../application/use-cases/get-public-posts.use-case';
import { GetPublicPostUseCase } from '../../application/use-cases/get-public-post.use-case';
import { CreatePostUseCase } from '../../application/use-cases/create-post.use-case';
import { UpdatePostUseCase } from '../../application/use-cases/update-post.use-case';
import { DeletePostUseCase } from '../../application/use-cases/delete-post.use-case';
import { ChangePostStatusUseCase } from '../../application/use-cases/change-post-status.use-case';
import { GetPostTransitionsUseCase } from '../../application/use-cases/get-post-transitions.use-case';
import { SavePostAsDraftUseCase } from '../../application/use-cases/save-post-as-draft.use-case';
import { GeneratePostPreviewUseCase } from '../../application/use-cases/generate-post-preview.use-case';

export const configureContainer = (container: ContainerInterface) => {
    // ========== STORAGE SERVICES ==========
    container.singleton('TokenStorage', () => new LocalTokenStorage());

    // ========== HTTP CLIENT ==========
    container.singleton('HttpClient', () => {
        const tokenStorage = container.get('TokenStorage') as TokenStorageInterface;
        // Get API base URL from environment or use default
        const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:8000/api';
        return new HttpClientService(tokenStorage, {
            baseURL: apiBaseUrl,
            timeout: 30000,
            retries: 3,
            retryDelay: 1000
        });
    });

    // ========== REPOSITORIES ==========
    container.singleton('UserRepository', () => {
        const httpClient = container.get('HttpClient') as HttpClientInterface;
        return new HttpUserRepository(httpClient);
    });

    container.singleton('PostRepository', () => {
        const httpClient = container.get('HttpClient') as HttpClientInterface;
        return new HttpPostRepository(httpClient);
    });

    // ========== SERVICES ==========
    container.singleton('AuthorizationService', () => new AuthorizationService());

    // ========== USE CASES - AUTH ==========
    container.bind('LoginUseCase', () => {
        const userRepository = container.get('UserRepository') as UserRepositoryInterface;
        return new LoginUserUseCase(userRepository);
    });

    container.bind('RegisterUseCase', () => {
        const userRepository = container.get('UserRepository') as UserRepositoryInterface;
        return new RegisterUserUseCase(userRepository);
    });

    container.bind('LogoutUseCase', () => {
        const userRepository = container.get('UserRepository') as UserRepositoryInterface;
        return new LogoutUserUseCase(userRepository);
    });

    // ========== USE CASES - POSTS ==========
    container.bind('GetPostsUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new GetPostsUseCase(postRepository);
    });

    container.bind('GetPostUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new GetPostUseCase(postRepository);
    });

    container.bind('GetPublicPostsUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new GetPublicPostsUseCase(postRepository);
    });

    container.bind('GetPublicPostUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new GetPublicPostUseCase(postRepository);
    });

    container.bind('CreatePostUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new CreatePostUseCase(postRepository);
    });

    container.bind('UpdatePostUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new UpdatePostUseCase(postRepository);
    });

    container.bind('DeletePostUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new DeletePostUseCase(postRepository);
    });

    container.bind('ChangePostStatusUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new ChangePostStatusUseCase(postRepository);
    });

    container.bind('GetPostTransitionsUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new GetPostTransitionsUseCase(postRepository);
    });

    container.bind('SavePostAsDraftUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new SavePostAsDraftUseCase(postRepository);
    });

    container.bind('GeneratePostPreviewUseCase', () => {
        const postRepository = container.get('PostRepository') as PostRepositoryInterface;
        return new GeneratePostPreviewUseCase(postRepository);
    });
};