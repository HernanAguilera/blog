<template>
  <article class="prose prose-lg dark:prose-invert max-w-none">
    <!-- Contenido del post -->
    <div
      class="post-content"
      v-html="sanitizedContent"
    ></div>
  </article>
</template>

<script setup lang="ts">
interface Props {
  content: string
}

const props = defineProps<Props>()

// Sanitizar contenido HTML (básico)
// En producción deberías usar una librería como DOMPurify
const sanitizedContent = computed(() => {
  // Por ahora, confiamos en que el contenido del backend ya está sanitizado
  // TODO: Implementar sanitización del lado cliente si es necesario
  return props.content
})
</script>

<style scoped>
/* Estilos específicos para el contenido del post */
.post-content {
  @apply text-gray-900 dark:text-gray-100;
}

/* Estilos para elementos HTML dentro del contenido */
.post-content :deep(h1) {
  @apply text-3xl font-bold mt-8 mb-4 text-gray-900 dark:text-white;
}

.post-content :deep(h2) {
  @apply text-2xl font-bold mt-6 mb-3 text-gray-900 dark:text-white;
}

.post-content :deep(h3) {
  @apply text-xl font-semibold mt-4 mb-2 text-gray-900 dark:text-white;
}

.post-content :deep(p) {
  @apply mb-4 leading-relaxed;
}

.post-content :deep(ul) {
  @apply list-disc list-inside mb-4 space-y-1;
}

.post-content :deep(ol) {
  @apply list-decimal list-inside mb-4 space-y-1;
}

.post-content :deep(li) {
  @apply leading-relaxed;
}

.post-content :deep(blockquote) {
  @apply border-l-4 border-blue-500 pl-4 italic my-4 text-gray-700 dark:text-gray-300;
}

.post-content :deep(code) {
  @apply bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded text-sm font-mono text-red-600 dark:text-red-400;
}

.post-content :deep(pre) {
  @apply bg-gray-100 dark:bg-gray-800 p-4 rounded-lg overflow-x-auto mb-4;
}

.post-content :deep(pre code) {
  @apply bg-transparent text-gray-900 dark:text-gray-100 p-0;
}

.post-content :deep(a) {
  @apply text-blue-600 dark:text-blue-400 hover:underline;
}

.post-content :deep(img) {
  @apply max-w-full h-auto rounded-lg shadow-md my-4;
}

.post-content :deep(table) {
  @apply min-w-full border-collapse border border-gray-300 dark:border-gray-600 my-4;
}

.post-content :deep(th) {
  @apply border border-gray-300 dark:border-gray-600 px-4 py-2 bg-gray-100 dark:bg-gray-700 font-semibold;
}

.post-content :deep(td) {
  @apply border border-gray-300 dark:border-gray-600 px-4 py-2;
}

/* Estilos para contenido de Quill.js */
.post-content :deep(.ql-editor) {
  @apply p-0;
}

.post-content :deep(.ql-align-center) {
  @apply text-center;
}

.post-content :deep(.ql-align-right) {
  @apply text-right;
}

.post-content :deep(.ql-align-justify) {
  @apply text-justify;
}
</style>