import type { PublicPostData } from '../types/public-post.types'

export interface SEOConfig {
  title?: string
  description?: string
  image?: string
  url?: string
  type?: 'website' | 'article'
  siteName?: string
  locale?: string
  publishedTime?: string
  modifiedTime?: string
  authorName?: string
  tags?: string[]
}

/**
 * Composable para manejar SEO y meta tags de manera centralizada
 * Proporciona configuración consistente de meta tags, Open Graph y structured data
 */
export const useSEO = () => {
  const route = useRoute()
  const runtimeConfig = useRuntimeConfig()

  // Configuración base del sitio
  const siteConfig = {
    siteName: 'BlogV2',
    defaultTitle: 'BlogV2 - Blog Personal',
    defaultDescription: 'Blog personal sobre tecnología, desarrollo y más',
    defaultImage: '/images/og-default.jpg',
    baseUrl: runtimeConfig.public.baseUrl || 'http://localhost:3000',
    locale: 'es_ES',
    author: 'BlogV2'
  }

  /**
   * Configurar SEO para páginas generales
   */
  const setSEO = (config: SEOConfig) => {
    const title = config.title
      ? `${config.title} | ${siteConfig.siteName}`
      : siteConfig.defaultTitle

    const description = config.description || siteConfig.defaultDescription
    const image = config.image || siteConfig.defaultImage
    const url = config.url || `${siteConfig.baseUrl}${route.path}`
    const type = config.type || 'website'

    // Configurar meta tags
    useHead({
      title,
      meta: [
        // Meta tags básicos
        { name: 'description', content: description },
        { name: 'author', content: siteConfig.author },
        { name: 'robots', content: 'index,follow' },

        // Open Graph
        { property: 'og:title', content: title },
        { property: 'og:description', content: description },
        { property: 'og:image', content: image.startsWith('http') ? image : `${siteConfig.baseUrl}${image}` },
        { property: 'og:url', content: url },
        { property: 'og:type', content: type },
        { property: 'og:site_name', content: siteConfig.siteName },
        { property: 'og:locale', content: config.locale || siteConfig.locale },

        // Twitter Card
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'twitter:title', content: title },
        { name: 'twitter:description', content: description },
        { name: 'twitter:image', content: image.startsWith('http') ? image : `${siteConfig.baseUrl}${image}` },

        // Artículo específico
        ...(type === 'article' && config.publishedTime ? [
          { property: 'article:published_time', content: config.publishedTime },
          { property: 'article:author', content: config.authorName || siteConfig.author }
        ] : []),

        ...(type === 'article' && config.modifiedTime ? [
          { property: 'article:modified_time', content: config.modifiedTime }
        ] : []),

        ...(config.tags ? config.tags.map(tag => ({ property: 'article:tag', content: tag })) : [])
      ],
      link: [
        { rel: 'canonical', href: url }
      ]
    })

    // Structured Data para artículos
    if (type === 'article') {
      const structuredData = {
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: config.title,
        description: description,
        image: image.startsWith('http') ? image : `${siteConfig.baseUrl}${image}`,
        author: {
          '@type': 'Person',
          name: config.authorName || siteConfig.author
        },
        publisher: {
          '@type': 'Organization',
          name: siteConfig.siteName,
          logo: {
            '@type': 'ImageObject',
            url: `${siteConfig.baseUrl}/images/logo.png`
          }
        },
        datePublished: config.publishedTime,
        dateModified: config.modifiedTime || config.publishedTime,
        mainEntityOfPage: {
          '@type': 'WebPage',
          '@id': url
        }
      }

      useHead({
        script: [
          {
            type: 'application/ld+json',
            innerHTML: JSON.stringify(structuredData)
          }
        ]
      })
    }
  }

  /**
   * Configurar SEO específico para posts del blog
   */
  const setPostSEO = (post: PublicPostData, canonicalUrl?: string) => {
    const config: SEOConfig = {
      title: post.title,
      description: post.excerpt || `Lee el artículo completo: ${post.title}`,
      url: canonicalUrl,
      type: 'article',
      publishedTime: post.publishedAt,
      modifiedTime: post.updatedAt,
      authorName: siteConfig.author,
      tags: post.tags
    }

    setSEO(config)
  }

  /**
   * Configurar SEO para listado de posts
   */
  const setPostListSEO = (page?: number) => {
    const title = page && page > 1 ? `Blog - Página ${page}` : 'Blog'
    const description = 'Artículos sobre tecnología, desarrollo y más en nuestro blog'

    setSEO({
      title,
      description,
      type: 'website'
    })
  }

  /**
   * Configurar SEO para páginas de error
   */
  const setErrorSEO = (errorCode: number) => {
    const titles: Record<number, string> = {
      404: 'Página no encontrada',
      500: 'Error interno del servidor',
      403: 'Acceso denegado'
    }

    setSEO({
      title: titles[errorCode] || 'Error',
      description: `Error ${errorCode} - ${titles[errorCode] || 'Ha ocurrido un error'}`
    })
  }

  /**
   * Generar URL canónica para posts
   */
  const generatePostCanonicalUrl = (post: PublicPostData): string => {
    const publishedDate = new Date(post.publishedAt || post.createdAt || new Date())
    const year = publishedDate.getFullYear()
    const month = String(publishedDate.getMonth() + 1).padStart(2, '0')

    return `${siteConfig.baseUrl}/${year}/${month}/${post.slug}`
  }

  return {
    setSEO,
    setPostSEO,
    setPostListSEO,
    setErrorSEO,
    generatePostCanonicalUrl,
    siteConfig
  }
}