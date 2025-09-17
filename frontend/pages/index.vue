<template>
  <NuxtLayout name="public">
    <div class="min-h-screen">
      <!-- Hero Section -->
      <section class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-12 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
              Bienvenido a
              <span class="text-blue-600 dark:text-blue-400">BlogV2</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
              Descubre artículos interesantes, tutoriales y reflexiones sobre tecnología, desarrollo y más.
            </p>

            <!-- Search bar -->
            <div class="max-w-md mx-auto mb-8">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Buscar artículos..."
                  class="w-full px-4 py-3 pl-10 pr-4 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  @keyup.enter="handleSearch"
                >
                <svg
                  class="absolute left-3 top-3.5 h-5 w-5 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Posts Section -->
      <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <!-- Section Header -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ searchQuery ? `Resultados para "${searchQuery}"` : 'Últimos Artículos' }}
              </h2>
              <p class="text-gray-600 dark:text-gray-400">
                {{ searchQuery ? '' : 'Mantente al día con nuestras últimas publicaciones' }}
              </p>
            </div>

            <!-- Clear search -->
            <button
              v-if="searchQuery"
              class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
              @click="clearSearch"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Limpiar búsqueda
            </button>
          </div>

          <!-- Posts Grid -->
          <PublicPostGrid
            :posts="posts"
            :loading="loading"
            :error="error"
            :search-query="searchQuery"
            @retry="handleRetry"
          />

          <!-- Pagination -->
          <div v-if="pagination && posts.length > 0" class="mt-12">
            <PublicPagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </section>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
// Composables
const {
  posts,
  loading,
  error,
  pagination,
  loadPosts,
  searchPosts,
  goToPage
} = usePublicPosts()

const { setPostListSEO } = useSEO()

// State
const searchQuery = ref('')

// SEO Configuration
const currentPage = computed(() => pagination.value?.current_page || 1)

// Configure SEO
watchEffect(() => {
  setPostListSEO(currentPage.value)
})

// Methods
const handleSearch = async () => {
  if (searchQuery.value.trim()) {
    await searchPosts(searchQuery.value.trim())
  } else {
    await loadPosts()
  }
}

const clearSearch = async () => {
  searchQuery.value = ''
  await loadPosts()
}

const handlePageChange = async (page: number) => {
  await goToPage(page)
  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleRetry = async () => {
  if (searchQuery.value) {
    await searchPosts(searchQuery.value)
  } else {
    await loadPosts()
  }
}

// Load posts on mount
onMounted(async () => {
  await loadPosts()
})
</script>