/**
 * Tipos para datos de posts en el frontend público
 * Estas interfaces simplifican el uso de datos de posts en componentes
 */
export interface PublicPostData {
  id: string
  title: string
  content: string
  slug: string
  status: string
  authorId: string
  excerpt?: string
  featuredImage?: string
  tags?: string[]
  readingTime?: number
  publishedAt?: string
  createdAt?: string
  updatedAt?: string
}

/**
 * Adaptador para convertir entidad Post a datos públicos
 */
export class PublicPostAdapter {
  static fromPost(post: any): PublicPostData {
    return {
      id: post.getId().value(),
      title: post.getTitle().value(),
      content: post.getContent().value(),
      slug: post.getSlug().value(),
      status: post.getStatus().value(),
      authorId: post.getAuthorId().value(),
      excerpt: post.getExcerpt(160),
      featuredImage: undefined, // TODO: Implementar cuando se añada soporte para imágenes
      tags: [], // TODO: Implementar cuando se añada soporte para tags
      readingTime: Math.ceil(post.getWordCount() / 200), // Aproximadamente 200 palabras por minuto
      publishedAt: post.getPublishedAt()?.toISOString(),
      createdAt: post.getCreatedAt()?.toISOString(),
      updatedAt: post.getUpdatedAt()?.toISOString()
    }
  }

  static fromApiResponse(data: any): PublicPostData {
    return {
      id: String(data.id),
      title: data.title,
      content: data.content,
      slug: data.slug,
      status: data.status,
      authorId: String(data.author_id || data.authorId),
      excerpt: data.excerpt,
      featuredImage: data.featured_image || data.featuredImage,
      tags: data.tags || [],
      readingTime: data.reading_time || data.readingTime,
      publishedAt: data.published_at || data.publishedAt,
      createdAt: data.created_at || data.createdAt,
      updatedAt: data.updated_at || data.updatedAt
    }
  }
}