import type { PublicPostData } from '../types/public-post.types'
import type { LoadingState } from '../../shared/types/common.types'
import { usePostsStore } from '../stores/posts.store'

export interface PublicPostState {
  post: PublicPostData | null
  loading: LoadingState
  error: string | null
}

/**
 * Composable para manejar un post público individual
 * Gestiona la carga y estado de un post específico para lectura pública
 */
export const usePublicPost = () => {
  const postsStore = usePostsStore()

  // Estado reactivo con conversión de datos
  const state = computed<PublicPostState>(() => ({
    post: postsStore.currentPost ? {
      id: postsStore.currentPost.getId().value(),
      title: postsStore.currentPost.getTitle().value(),
      content: postsStore.currentPost.getContent().value(),
      slug: postsStore.currentPost.getSlug().value(),
      status: postsStore.currentPost.getStatus().value(),
      authorId: postsStore.currentPost.getAuthorId().value(),
      excerpt: postsStore.currentPost.getExcerpt(160),
      featuredImage: undefined,
      tags: [],
      readingTime: Math.ceil(postsStore.currentPost.getWordCount() / 200),
      publishedAt: postsStore.currentPost.getPublishedAt()?.toISOString(),
      createdAt: postsStore.currentPost.getCreatedAt()?.toISOString(),
      updatedAt: postsStore.currentPost.getUpdatedAt()?.toISOString()
    } : null,
    loading: postsStore.isLoading ? 'loading' : 'idle',
    error: postsStore.error
  }))

  /**
   * Cargar post público por slug y fecha
   */
  const loadPost = async (year: string, month: string, slug: string) => {
    try {
      // Construir el slug completo para la búsqueda
      const fullSlug = slug
      await postsStore.fetchPublicPost(fullSlug)
    } catch (error) {
      console.error('Error loading public post:', error)
    }
  }

  /**
   * Cargar post por slug simple (fallback)
   */
  const loadPostBySlug = async (slug: string) => {
    try {
      await postsStore.fetchPublicPost(slug)
    } catch (error) {
      console.error('Error loading post by slug:', error)
    }
  }

  /**
   * Limpiar estado actual
   */
  const clearPost = () => {
    postsStore.setCurrentPost(null)
  }

  /**
   * Obtener URL canónica del post
   */
  const getCanonicalUrl = (baseUrl: string) => {
    if (!state.value.post) return baseUrl

    const publishedDate = new Date(state.value.post.publishedAt || state.value.post.createdAt || new Date())
    const year = publishedDate.getFullYear()
    const month = String(publishedDate.getMonth() + 1).padStart(2, '0')

    return `${baseUrl}/${year}/${month}/${state.value.post.slug}`
  }

  /**
   * Verificar si el post es público
   */
  const isPublic = computed(() => {
    return state.value.post?.status === 'published'
  })

  /**
   * Verificar si hay un post cargado
   */
  const hasPost = computed(() => {
    return state.value.post !== null
  })

  /**
   * Obtener posts relacionados (placeholder para futura implementación)
   */
  const getRelatedPosts = async () => {
    // TODO: Implementar lógica para posts relacionados
    return []
  }

  return {
    // Estado
    post: computed(() => state.value.post),
    loading: computed(() => state.value.loading),
    error: computed(() => state.value.error),

    // Computed
    isPublic,
    hasPost,

    // Métodos
    loadPost,
    loadPostBySlug,
    clearPost,
    getCanonicalUrl,
    getRelatedPosts
  }
}