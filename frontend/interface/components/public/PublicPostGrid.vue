<template>
  <div>
    <!-- Loading State -->
    <div v-if="loading === 'loading'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="i in 6"
        :key="i"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-pulse"
      >
        <div class="aspect-video bg-gray-200 dark:bg-gray-700"></div>
        <div class="p-6">
          <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded mb-3"></div>
          <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded mb-3"></div>
          <div class="space-y-2">
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-4/5"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Posts Grid -->
    <div
      v-else-if="posts.length > 0"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <PublicPostCard
        v-for="post in posts"
        :key="post.id"
        :post="post"
      />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="loading === 'idle' && posts.length === 0"
      class="text-center py-12"
    >
      <svg
        class="w-24 h-24 text-gray-300 dark:text-gray-600 mx-auto mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
        />
      </svg>
      <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">
        No hay posts disponibles
      </h3>
      <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
        {{ searchQuery ? 'No se encontraron posts con tu búsqueda.' : 'Aún no se han publicado posts en este blog.' }}
      </p>
    </div>

    <!-- Error State -->
    <div
      v-if="error"
      class="text-center py-12"
    >
      <svg
        class="w-24 h-24 text-red-300 mx-auto mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
        />
      </svg>
      <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">
        Error al cargar posts
      </h3>
      <p class="text-gray-500 dark:text-gray-400 mb-4">
        {{ error }}
      </p>
      <button
        @click="$emit('retry')"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Reintentar
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PublicPostData } from '../../types/public-post.types'
import type { LoadingState } from '../../../shared/types/common.types'

interface Props {
  posts: PublicPostData[]
  loading: LoadingState
  error: string | null
  searchQuery?: string
}

defineProps<Props>()

defineEmits<{
  retry: []
}>()
</script>