# Frontend Service Container (Dependency Injection)

## Service Container y Bindings

```typescript
// shared/container/bindings.ts
export const configureContainer = (container: ContainerInterface) => {
    // ...
    container.singleton('TokenStorage', () => new LocalTokenStorage());
    container.singleton('HttpClient', () => new HttpClient(/*...*/));
    container.singleton('UserRepository', () => new HttpUserRepository(/*...*/));
    // ...
    container.bind('LoginUseCase', () => new LoginUseCase(/*...*/));
    // ...
};
```
