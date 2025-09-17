<template>
  <article class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <!-- Featured Image -->
    <div
      v-if="post.featuredImage"
      class="aspect-video bg-gray-100 dark:bg-gray-700 overflow-hidden"
    >
      <img
        :src="post.featuredImage"
        :alt="post.title"
        class="w-full h-full object-cover hover:scale-105 transition-transform duration-200"
        loading="lazy"
      />
    </div>

    <!-- Sin imagen, usar gradiente -->
    <div
      v-else
      class="aspect-video bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center"
    >
      <svg
        class="w-16 h-16 text-white opacity-70"
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
    </div>

    <!-- Content -->
    <div class="p-6">
      <!-- Meta info -->
      <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
        <time :datetime="formatDateISO(post.publishedAt)" class="mr-3">
          {{ formatDate(post.publishedAt) }}
        </time>
        <span v-if="post.readingTime" class="flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ post.readingTime }} min
        </span>
      </div>

      <!-- Title -->
      <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
        <NuxtLink :to="postUrl" class="block">
          {{ post.title }}
        </NuxtLink>
      </h2>

      <!-- Excerpt -->
      <p
        v-if="post.excerpt"
        class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4 line-clamp-3"
      >
        {{ post.excerpt }}
      </p>

      <!-- Tags -->
      <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2 mb-4">
        <span
          v-for="tag in post.tags.slice(0, 3)"
          :key="tag"
          class="inline-block px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900 rounded"
        >
          #{{ tag }}
        </span>
        <span
          v-if="post.tags.length > 3"
          class="inline-block px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 dark:text-gray-400 dark:bg-gray-700 rounded"
        >
          +{{ post.tags.length - 3 }}
        </span>
      </div>

      <!-- Read more link -->
      <NuxtLink
        :to="postUrl"
        class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
      >
        Leer más
        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </NuxtLink>
    </div>
  </article>
</template>

<script setup lang="ts">
import type { PublicPostData } from '../../types/public-post.types'

interface Props {
  post: PublicPostData
}

const props = defineProps<Props>()

// Generar URL del post con formato /{year}/{month}/{slug}
const postUrl = computed(() => {
  const publishedDate = new Date(props.post.publishedAt || props.post.createdAt || new Date())
  const year = publishedDate.getFullYear()
  const month = String(publishedDate.getMonth() + 1).padStart(2, '0')
  return `/${year}/${month}/${props.post.slug}`
})

// Formatear fecha para mostrar
const formatDate = (dateString?: string) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Formatear fecha para atributo datetime
const formatDateISO = (dateString?: string) => {
  if (!dateString) return ''
  return new Date(dateString).toISOString()
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>