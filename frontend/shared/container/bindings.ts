import type { Container } from '~/shared/types/container'
import { TOKENS } from '~/shared/types/container'

// Infrastructure implementations (to be created)
// import { HttpClient } from '~/infrastructure/http/http-client'
// import { LocalTokenStorage } from '~/infrastructure/storage/local-token-storage'

// Repositories (to be created)
// import { HttpUserRepository } from '~/infrastructure/http/user-repository'
// import { HttpPostRepository } from '~/infrastructure/http/post-repository'

// Use Cases (to be created)
// import { LoginUseCase } from '~/application/use-cases/auth/login-use-case'
// import { LogoutUseCase } from '~/application/use-cases/auth/logout-use-case'
// import { GetPostsUseCase } from '~/application/use-cases/posts/get-posts-use-case'

export function configureContainer(container: Container): void {
  // Infrastructure bindings
  container.singleton(TOKENS.HTTP_CLIENT, () => {
    // return new HttpClient(useRuntimeConfig().public.apiBase)
    throw new Error('HttpClient not implemented yet')
  })

  container.singleton(TOKENS.LOCAL_STORAGE, () => {
    // return new LocalTokenStorage()
    throw new Error('LocalStorage not implemented yet')
  })

  // Repository bindings
  container.singleton(TOKENS.USER_REPOSITORY, () => {
    // const httpClient = container.resolve(TOKENS.HTTP_CLIENT)
    // return new HttpUserRepository(httpClient)
    throw new Error('UserRepository not implemented yet')
  })

  container.singleton(TOKENS.POST_REPOSITORY, () => {
    // const httpClient = container.resolve(TOKENS.HTTP_CLIENT)
    // return new HttpPostRepository(httpClient)
    throw new Error('PostRepository not implemented yet')
  })

  // Use Case bindings
  container.bind(TOKENS.LOGIN_USE_CASE, () => {
    // const userRepository = container.resolve(TOKENS.USER_REPOSITORY)
    // const tokenStorage = container.resolve(TOKENS.LOCAL_STORAGE)
    // return new LoginUseCase(userRepository, tokenStorage)
    throw new Error('LoginUseCase not implemented yet')
  })

  container.bind(TOKENS.LOGOUT_USE_CASE, () => {
    // const tokenStorage = container.resolve(TOKENS.LOCAL_STORAGE)
    // return new LogoutUseCase(tokenStorage)
    throw new Error('LogoutUseCase not implemented yet')
  })

  container.bind(TOKENS.GET_POSTS_USE_CASE, () => {
    // const postRepository = container.resolve(TOKENS.POST_REPOSITORY)
    // return new GetPostsUseCase(postRepository)
    throw new Error('GetPostsUseCase not implemented yet')
  })
}

// Helper function to get services from container
export function useService<T>(token: string | symbol): T {
  const { $container } = useNuxtApp()
  return $container.resolve<T>(token)
}
