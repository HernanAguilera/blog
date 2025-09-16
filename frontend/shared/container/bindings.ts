import type { ContainerInterface } from '../contracts/container';

// Infrastructure imports
import { LocalTokenStorage } from '../../infrastructure/storage/local-token.storage';
import { HttpClientService } from '../../infrastructure/services/http-client.service';
import { HttpUserRepository } from '../../infrastructure/repositories/http-user.repository';

// Infrastructure types
import type { TokenStorageInterface } from '../../infrastructure/storage/token-storage.interface';
import type { HttpClientInterface } from '../../infrastructure/services/http-client.interface';
import type { UserRepositoryInterface } from '../../domain/repositories/user-repository.interface';

// Application imports
import { AuthorizationService } from '../../application/services/authorization.service';
import { LoginUserUseCase } from '../../application/use-cases/login-user.use-case';
import { RegisterUserUseCase } from '../../application/use-cases/register-user.use-case';
import { LogoutUserUseCase } from '../../application/use-cases/logout-user.use-case';

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
};