import type { ContainerInterface } from '../types/container';

export const configureContainer = (container: ContainerInterface) => {
    // const config = useRuntimeConfig(); // Solo disponible en composables de Nuxt

    // ========== STORAGE SERVICES ==========
    // container.singleton('TokenStorage', () => new LocalTokenStorage());
    // container.singleton('CacheStorage', () => new BrowserCacheStorage());
    // container.singleton('SessionStorage', () => new BrowserSessionStorage());

    // ========== HTTP CLIENT ==========
    // container.singleton('HttpClient', () =>
    //     new HttpClient(
    //         config.public.apiBaseUrl,
    //         container.get('TokenStorage'),
    //         {
    //             timeout: 30000,
    //             retries: 3,
    //             retryDelay: 1000
    //         }
    //     )
    // );

    // ========== REPOSITORIES ==========
    // container.singleton('UserRepository', () => new HttpUserRepository(/*...*/));
    // container.singleton('PostRepository', () => new HttpPostRepository(/*...*/));

    // ========== SERVICES ==========
    // container.singleton('ValidationService', () => new ValidationService());
    // container.singleton('NotificationService', () => new NotificationService());

    // ========== USE CASES - AUTH ==========
    // container.bind('LoginUseCase', () => new LoginUseCase(/*...*/));
    // container.bind('LogoutUseCase', () => new LogoutUseCase(/*...*/));

    // TODO: Implementar bindings cuando se desarrollen los servicios correspondientes
};