# Frontend Service Container (Dependency Injection)

## Service Container y Bindings

```typescript
// shared/container/bindings.ts
export const configureContainer = (container: ContainerInterface) => {
    const config = useRuntimeConfig();

    // ========== STORAGE SERVICES ==========
    container.singleton('TokenStorage', () => new LocalTokenStorage());
    container.singleton('CacheStorage', () => new BrowserCacheStorage());
    container.singleton('SessionStorage', () => new BrowserSessionStorage());

    // ========== HTTP CLIENT ==========
    container.singleton('HttpClient', () => 
        new HttpClient(
            config.public.apiBaseUrl,
            container.get('TokenStorage'),
            {
                timeout: 30000,
                retries: 3,
                retryDelay: 1000
            }
        )
    );

    // ========== REPOSITORIES ==========
    container.singleton('UserRepository', () => new HttpUserRepository(/*...*/));
    container.singleton('PostRepository', () => new HttpPostRepository(/*...*/));
    // ... more repositories

    // ========== SERVICES ==========
    container.singleton('ValidationService', () => new ValidationService());
    container.singleton('NotificationService', () => new NotificationService());
    // ... more services

    // ========== USE CASES - AUTH ==========
    container.bind('LoginUseCase', () => new LoginUseCase(/*...*/));
    container.bind('LogoutUseCase', () => new LogoutUseCase(/*...*/));
    // ... more use cases
};
```
