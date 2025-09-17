import type { PublicPostData } from '../types/public-post.types'
import type { LoadingState } from '../../shared/types/common.types'
import { usePostsStore } from '../stores/posts.store'

export interface PublicPostsState {
  posts: PublicPostData[]
  loading: LoadingState
  error: string | null
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  } | null
}

export interface PublicPostsFilters {
  page?: number
  limit?: number
  search?: string
}

/**
 * Composable para manejar posts públicos en el frontend público
 * Simplifica la interacción con el store de posts para casos de uso públicos
 */
export const usePublicPosts = () => {
  const postsStore = usePostsStore()

  // Estado reactivo con conversión de datos
  const state = computed<PublicPostsState>(() => ({
    posts: postsStore.posts.map((post: any) => ({
      id: post.getId().value(),
      title: post.getTitle().value(),
      content: post.getContent().value(),
      slug: post.getSlug().value(),
      status: post.getStatus().value(),
      authorId: post.getAuthorId().value(),
      excerpt: post.getExcerpt(160),
      featuredImage: undefined,
      tags: [],
      readingTime: Math.ceil(post.getWordCount() / 200),
      publishedAt: post.getPublishedAt()?.toISOString(),
      createdAt: post.getCreatedAt()?.toISOString(),
      updatedAt: post.getUpdatedAt()?.toISOString()
    })),
    loading: postsStore.isLoading ? 'loading' : 'idle',
    error: postsStore.error,
    pagination: postsStore.pagination
  }))

  /**
   * Cargar posts públicos
   */
  const loadPosts = async (filters: PublicPostsFilters = {}) => {
    try {
      await postsStore.fetchPublicPosts()
    } catch (error) {
      console.error('Error loading public posts:', error)
    }
  }

  /**
   * Cargar más posts (para paginación)
   */
  const loadMore = async () => {
    if (!state.value.pagination) return

    const nextPage = state.value.pagination.current_page + 1
    if (nextPage <= state.value.pagination.last_page) {
      await loadPosts({ page: nextPage })
    }
  }

  /**
   * Buscar posts
   */
  const searchPosts = async (searchQuery: string) => {
    await loadPosts({ search: searchQuery, page: 1 })
  }

  /**
   * Verificar si hay más páginas
   */
  const hasMorePages = computed(() => {
    if (!state.value.pagination) return false
    return state.value.pagination.current_page < state.value.pagination.last_page
  })

  /**
   * Verificar si estamos en la primera página
   */
  const isFirstPage = computed(() => {
    if (!state.value.pagination) return true
    return state.value.pagination.current_page === 1
  })

  /**
   * Ir a una página específica
   */
  const goToPage = async (page: number) => {
    await loadPosts({ page })
  }

  return {
    // Estado
    posts: computed(() => state.value.posts),
    loading: computed(() => state.value.loading),
    error: computed(() => state.value.error),
    pagination: computed(() => state.value.pagination),

    // Computed
    hasMorePages,
    isFirstPage,

    // Métodos
    loadPosts,
    loadMore,
    searchPosts,
    goToPage
  }
}