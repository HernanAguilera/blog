<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-6 border-b border-gray-200 dark:border-gray-700">
    <!-- Información principal del post -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <!-- Fecha de publicación -->
      <div class="flex items-center text-gray-600 dark:text-gray-400">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <time :datetime="formatDateISO(post.publishedAt)" class="text-sm font-medium">
          {{ formatDate(post.publishedAt) }}
        </time>
      </div>

      <!-- Tiempo de lectura -->
      <div v-if="post.readingTime" class="flex items-center text-gray-600 dark:text-gray-400">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium">{{ post.readingTime }} min de lectura</span>
      </div>

      <!-- Estado (solo si no está publicado) -->
      <div v-if="post.status !== 'published'" class="flex items-center">
        <span
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
          :class="getStatusClasses(post.status)"
        >
          {{ getStatusLabel(post.status) }}
        </span>
      </div>
    </div>

    <!-- Tags -->
    <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2">
      <span
        v-for="tag in post.tags"
        :key="tag"
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors cursor-pointer"
        @click="$emit('tag-click', tag)"
      >
        #{{ tag }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PublicPostData } from '../../types/public-post.types'

interface Props {
  post: PublicPostData
}

defineProps<Props>()

defineEmits<{
  'tag-click': [tag: string]
}>()

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

// Obtener clases CSS para el estado
const getStatusClasses = (status: string) => {
  switch (status) {
    case 'draft':
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
    case 'scheduled':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-300'
    case 'archived':
      return 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-300'
    default:
      return 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-300'
  }
}

// Obtener etiqueta del estado
const getStatusLabel = (status: string) => {
  switch (status) {
    case 'draft':
      return 'Borrador'
    case 'scheduled':
      return 'Programado'
    case 'archived':
      return 'Archivado'
    default:
      return status.charAt(0).toUpperCase() + status.slice(1)
  }
}
</script>