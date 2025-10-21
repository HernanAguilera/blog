import type { ContainerInterface } from '../contracts/container';

// Infrastructure imports
import { LocalTokenStorage } from '../../infrastructure/storage/local-token.storage';
import { HttpClientService } from '../../infrastructure/services/http-client.service';
import { ToastNotificationService } from '../../infrastructure/services/toast-notification.service';
import { HttpUserRepository } from '../../infrastructure/repositories/http-user.repository';
import { HttpPostRepository } from '../../infrastructure/repositories/http-post.repository';
import { HttpCommentRepository } from '../../infrastructure/repositories/http-comment-repository';
import { CommentAPI } from '../../infrastructure/api/comment-api';

// Infrastructure types
import type { TokenStorageInterface } from '../../infrastructure/storage/token-storage.interface';
import type { HttpClientInterface } from '../../infrastructure/services/http-client.interface';
import type { NotificationServiceInterface } from '../../application/services/notification-service.interface';
import type { UserRepositoryInterface } from '../../domain/repositories/user-repository.interface';
import type { PostRepositoryInterface } from '../../domain/repositories/post-repository.interface';
import type { CommentRepositoryInterface } from '../../domain/repositories/comment-repository.interface';

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

// Comment use cases
import { CreateCommentUseCase } from '../../application/use-cases/comment/create-comment.use-case';
import { CreateAnonymousCommentUseCase } from '../../application/use-cases/comment/create-anonymous-comment.use-case';
import { GetCommentsTreeUseCase } from '../../application/use-cases/comment/get-comments-tree.use-case';
import { GetCommentCountUseCase } from '../../application/use-cases/comment/get-comment-count.use-case';
import { ApproveCommentUseCase } from '../../application/use-cases/comment/approve-comment.use-case';
import { RejectCommentUseCase } from '../../application/use-cases/comment/reject-comment.use-case';
import { MarkCommentAsSpamUseCase } from '../../application/use-cases/comment/mark-comment-as-spam.use-case';
import { DeleteCommentUseCase } from '../../application/use-cases/comment/delete-comment.use-case';
import { GetPendingCommentsUseCase } from '../../application/use-cases/comment/get-pending-comments.use-case';
import { BulkApproveCommentsUseCase } from '../../application/use-cases/comment/bulk-approve-comments.use-case';
import { BulkRejectCommentsUseCase } from '../../application/use-cases/comment/bulk-reject-comments.use-case';
import { BulkDeleteCommentsUseCase } from '../../application/use-cases/comment/bulk-delete-comments.use-case';

export const configureContainer = (container: ContainerInterface) => {
    // ========== STORAGE SERVICES ==========
    container.singleton('TokenStorage', () => new LocalTokenStorage());

    // ========== NOTIFICATION SERVICE ==========
    // El servicio de notificaciones se registrará desde el plugin de Nuxt
    // para tener acceso a $toast. Ver: plugins/container.client.ts

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

    // ========== API CLIENTS - COMMENTS ==========
    container.singleton('CommentAPI', () => {
        const httpClient = container.get('HttpClient') as HttpClientInterface;
        return new CommentAPI(httpClient);
    });

    // ========== REPOSITORIES - COMMENTS ==========
    container.singleton('CommentRepository', () => {
        const commentAPI = container.get('CommentAPI') as CommentAPI;
        return new HttpCommentRepository(commentAPI);
    });

    // ========== USE CASES - COMMENTS (PUBLIC) ==========
    container.bind('GetCommentsTreeUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new GetCommentsTreeUseCase(commentRepository);
    });

    container.bind('GetCommentCountUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new GetCommentCountUseCase(commentRepository);
    });

    container.bind('CreateCommentUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new CreateCommentUseCase(commentRepository);
    });

    container.bind('CreateAnonymousCommentUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new CreateAnonymousCommentUseCase(commentRepository);
    });

    // ========== USE CASES - COMMENTS (ADMIN) ==========
    container.bind('GetPendingCommentsUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new GetPendingCommentsUseCase(commentRepository);
    });

    container.bind('ApproveCommentUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new ApproveCommentUseCase(commentRepository);
    });

    container.bind('RejectCommentUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new RejectCommentUseCase(commentRepository);
    });

    container.bind('MarkCommentAsSpamUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new MarkCommentAsSpamUseCase(commentRepository);
    });

    container.bind('DeleteCommentUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new DeleteCommentUseCase(commentRepository);
    });

    container.bind('BulkApproveCommentsUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new BulkApproveCommentsUseCase(commentRepository);
    });

    container.bind('BulkRejectCommentsUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new BulkRejectCommentsUseCase(commentRepository);
    });

    container.bind('BulkDeleteCommentsUseCase', () => {
        const commentRepository = container.get('CommentRepository') as CommentRepositoryInterface;
        return new BulkDeleteCommentsUseCase(commentRepository);
    });
};