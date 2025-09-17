<template>
  <div class="bg-white">
    <!-- Preview header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">
          Vista previa
        </h3>
        <button
          type="button"
          class="text-gray-400 hover:text-gray-500"
          @click="$emit('close')"
        >
          <svg
            class="h-5 w-5"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Preview content -->
    <div class="px-6 py-8">
      <article class="prose prose-lg max-w-none">
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
          {{ title || 'Sin título' }}
        </h1>

        <!-- Meta information -->
        <div class="text-sm text-gray-500 mb-8 flex items-center space-x-4">
          <span v-if="publishedAt">
            Publicado el {{ formatDate(publishedAt) }}
          </span>
          <span v-else-if="scheduledAt">
            Programado para {{ formatDate(scheduledAt) }}
          </span>
          <span v-else>
            Borrador
          </span>

          <span v-if="wordCount">
            {{ wordCount }} palabras
          </span>

          <span v-if="readingTime">
            {{ readingTime }} min de lectura
          </span>
        </div>

        <!-- Content -->
        <div
          v-if="content"
          class="prose-content"
          v-html="sanitizedContent"
        />
        <div
          v-else
          class="text-gray-500 italic"
        >
          No hay contenido para mostrar
        </div>
      </article>
    </div>

    <!-- Preview footer -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">
          Esta es una vista previa de como se verá el post publicado
        </div>

        <div class="flex space-x-3">
          <button
            type="button"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            @click="$emit('edit')"
          >
            Editar
          </button>

          <button
            v-if="canPublish"
            type="button"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            @click="$emit('publish')"
          >
            Publicar ahora
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  title: string;
  content: string;
  publishedAt?: string;
  scheduledAt?: string;
  canPublish?: boolean;
}

interface Emits {
  (e: 'close'): void;
  (e: 'edit'): void;
  (e: 'publish'): void;
}

const props = withDefaults(defineProps<Props>(), {
  canPublish: false
});

const emit = defineEmits<Emits>();

// Sanitize HTML content (basic implementation)
const sanitizedContent = computed(() => {
  if (!props.content) return '';

  // In a real implementation, use a proper HTML sanitizer like DOMPurify
  // For now, we'll trust the content as it comes from Quill.js
  return props.content;
});

// Calculate word count
const wordCount = computed(() => {
  if (!props.content) return 0;

  // Remove HTML tags and count words
  const plainText = props.content.replace(/<[^>]*>/g, '').trim();
  if (!plainText) return 0;

  return plainText.split(/\s+/).length;
});

// Calculate estimated reading time (average 200 words per minute)
const readingTime = computed(() => {
  if (!wordCount.value) return 0;
  return Math.ceil(wordCount.value / 200);
});

// Format date for display
const formatDate = (dateString: string): string => {
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch {
    return dateString;
  }
};
</script>

<style scoped>
.prose-content {
  @apply text-gray-900 leading-relaxed;
}

.prose-content h1,
.prose-content h2,
.prose-content h3,
.prose-content h4,
.prose-content h5,
.prose-content h6 {
  @apply font-bold text-gray-900 mt-8 mb-4;
}

.prose-content h1 { @apply text-2xl; }
.prose-content h2 { @apply text-xl; }
.prose-content h3 { @apply text-lg; }

.prose-content p {
  @apply mb-4;
}

.prose-content ul,
.prose-content ol {
  @apply mb-4 pl-6;
}

.prose-content li {
  @apply mb-2;
}

.prose-content blockquote {
  @apply border-l-4 border-gray-300 pl-4 italic text-gray-600 mb-4;
}

.prose-content code {
  @apply bg-gray-100 px-2 py-1 rounded text-sm font-mono;
}

.prose-content pre {
  @apply bg-gray-100 p-4 rounded overflow-x-auto mb-4;
}

.prose-content a {
  @apply text-blue-600 hover:text-blue-800 underline;
}

.prose-content img {
  @apply max-w-full h-auto mb-4;
}
</style>