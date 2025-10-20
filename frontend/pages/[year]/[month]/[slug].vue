<template>
  <NuxtLayout name="public">
    <div class="min-h-screen">
      <!-- Loading State -->
      <div v-if="loading === 'loading'" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="animate-pulse">
          <!-- Header skeleton -->
          <div class="mb-8">
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded mb-4"/>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"/>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"/>
          </div>

          <!-- Content skeleton -->
          <div class="space-y-4">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-5/6"/>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-4/5"/>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <svg class="w-24 h-24 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
          Post no encontrado
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          El artículo que buscas no existe o ha sido movido.
        </p>
        <NuxtLink
          to="/"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Volver al inicio
        </NuxtLink>
      </div>

      <!-- Post Content -->
      <article v-else-if="post && isPublic" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
              <NuxtLink to="/" class="hover:text-blue-600 dark:hover:text-blue-400">
                Inicio
              </NuxtLink>
            </li>
            <li>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </li>
            <li>
              <span class="text-gray-900 dark:text-white">{{ post.title }}</span>
            </li>
          </ol>
        </nav>

        <!-- Post Header -->
        <header class="mb-8">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
            {{ post.title }}
          </h1>

          <!-- Featured Image -->
          <div v-if="post.featuredImage" class="mb-8">
            <img
              :src="post.featuredImage"
              :alt="post.title"
              class="w-full h-64 sm:h-96 object-cover rounded-lg shadow-lg"
            >
          </div>

          <!-- Post Meta -->
          <PostMeta :post="post" @tag-click="handleTagClick" />
        </header>

        <!-- Post Content -->
        <div class="mb-8">
          <PostContent :content="post.content" />
        </div>

        <!-- Share Buttons -->
        <ShareButtons
          :title="post.title"
          :url="canonicalUrl"
          :excerpt="post.excerpt"
        />

        <!-- Comments Section -->
        <div class="mt-12">
          <CommentsList :post-slug="post.slug" />
        </div>

        <!-- Navigation to other posts -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <NuxtLink
              to="/"
              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Todos los artículos
            </NuxtLink>

            <div class="text-center">
              <p class="text-sm text-gray-500 dark:text-gray-400">
                ¿Te gustó este artículo? ¡Compártelo!
              </p>
            </div>
          </div>
        </div>
      </article>

      <!-- Not Public State -->
      <div v-else-if="post && !isPublic" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <svg class="w-24 h-24 text-yellow-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
          Artículo no disponible
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Este artículo aún no ha sido publicado o está siendo revisado.
        </p>
        <NuxtLink
          to="/"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Volver al inicio
        </NuxtLink>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
// Obtener parámetros de la ruta
const route = useRoute()
const { year, month, slug } = route.params

// Validar parámetros
if (!year || !month || !slug) {
  throw createError({
    statusCode: 404,
    statusMessage: 'Página no encontrada'
  })
}

// Composable para el post
const {
  post,
  loading,
  error,
  isPublic,
  hasPost,
  loadPost
} = usePublicPost()

// Composable SEO
const { setPostSEO, setErrorSEO, generatePostCanonicalUrl: seoCanonicalUrl } = useSEO()

// URL canónica computed
const canonicalUrl = computed(() => {
  if (!post.value) return ''
  return seoCanonicalUrl(post.value)
})

// SEO reactivo basado en el estado del post
watchEffect(() => {
  if (post.value && isPublic.value) {
    // Post público - configurar SEO normal
    setPostSEO(post.value, canonicalUrl.value)
  } else if (hasPost.value && !isPublic.value) {
    // Post existe pero no es público - SEO básico sin indexar
    useHead({
      title: 'Artículo no disponible',
      meta: [
        { name: 'description', content: 'Este artículo aún no ha sido publicado' },
        { name: 'robots', content: 'noindex,nofollow' }
      ]
    })
  } else if (error.value || (!loading.value && !hasPost.value)) {
    // Error o post no encontrado
    setErrorSEO(404)
  }
})


// Métodos
const handleTagClick = (tag: string) => {
  // TODO: Implementar navegación a página de tag
  navigateTo(`/?search=${encodeURIComponent('#' + tag)}`)
}

// Cargar post al montar
onMounted(async () => {
  await loadPost(String(year), String(month), String(slug))
})

// Limpiar al desmontar
onUnmounted(() => {
  // clearPost() // Opcional: mantener el post en cache
})
</script>