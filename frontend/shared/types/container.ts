// Container interface for dependency injection
export interface Container {
  bind<T>(token: string | symbol, factory: () => T): void
  singleton<T>(token: string | symbol, factory: () => T): void
  resolve<T>(token: string | symbol): T
  has(token: string | symbol): boolean
}

// Service tokens
export const TOKENS = {
  // Infrastructure
  HTTP_CLIENT: Symbol('HttpClient'),
  LOCAL_STORAGE: Symbol('LocalStorage'),

  // Repositories
  USER_REPOSITORY: Symbol('UserRepository'),
  POST_REPOSITORY: Symbol('PostRepository'),

  // Use Cases
  LOGIN_USE_CASE: Symbol('LoginUseCase'),
  LOGOUT_USE_CASE: Symbol('LogoutUseCase'),
  GET_POSTS_USE_CASE: Symbol('GetPostsUseCase'),

  // Services
  AUTH_SERVICE: Symbol('AuthService'),
  API_SERVICE: Symbol('ApiService'),
} as const

export type ServiceToken = (typeof TOKENS)[keyof typeof TOKENS]
