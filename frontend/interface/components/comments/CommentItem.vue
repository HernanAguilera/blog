<template>
  <div class="comment-item" :class="`comment-item--depth-${depth}`">
    <div class="comment-item__header">
      <div class="comment-item__avatar">
        <div class="comment-item__avatar-circle">
          {{ authorInitial }}
        </div>
      </div>

      <div class="comment-item__meta">
        <div class="comment-item__author">
          <span class="comment-item__author-name">{{ authorName }}</span>
          <span v-if="isUserComment" class="comment-item__badge comment-item__badge--user">
            Usuario registrado
          </span>
          <span v-else class="comment-item__badge comment-item__badge--anonymous">
            Anónimo
          </span>
        </div>
        <div class="comment-item__date">
          {{ formattedDate }}
        </div>
      </div>
    </div>

    <div class="comment-item__content" v-html="sanitizedContent"></div>

    <div class="comment-item__actions">
      <button
        v-if="canReply"
        type="button"
        class="comment-item__action-button"
        @click="handleReplyClick"
      >
        <svg class="comment-item__action-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
        </svg>
        Responder
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { CommentTreeNode } from '../../../domain/types/comment.types';
import DOMPurify from 'dompurify';

interface Props {
  comment: CommentTreeNode;
  depth: number;
  maxDepth?: number;
}

interface Emits {
  (e: 'reply-clicked', commentId: string): void;
}

const props = withDefaults(defineProps<Props>(), {
  maxDepth: 5,
});

const emit = defineEmits<Emits>();

// Computed
const isUserComment = computed(() => props.comment.author.type === 'registered');

const authorName = computed(() => {
  if (isUserComment.value) {
    return 'Usuario'; // TODO: obtener nombre real del usuario del backend
  }
  return props.comment.author.name || 'Anónimo';
});

const authorInitial = computed(() => {
  return authorName.value.charAt(0).toUpperCase();
});

const formattedDate = computed(() => {
  const date = new Date(props.comment.created_at);
  const now = new Date();
  const diffInMs = now.getTime() - date.getTime();
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));

  if (diffInHours < 1) {
    const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
    if (diffInMinutes < 1) {
      return 'Hace un momento';
    }
    return `Hace ${diffInMinutes} minuto${diffInMinutes > 1 ? 's' : ''}`;
  }

  if (diffInHours < 24) {
    return `Hace ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`;
  }

  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 7) {
    return `Hace ${diffInDays} día${diffInDays > 1 ? 's' : ''}`;
  }

  // Formato largo para fechas antiguas
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
});

const sanitizedContent = computed(() => {
  return DOMPurify.sanitize(props.comment.content, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'a', 'code', 'pre'],
    ALLOWED_ATTR: ['href', 'target', 'rel'],
  });
});

const canReply = computed(() => {
  // Los comentarios del árbol siempre están aprobados (el backend filtra por status='approved')
  return props.depth < props.maxDepth;
});

// Methods
const handleReplyClick = () => {
  emit('reply-clicked', props.comment.id);
};
</script>

<style scoped>
.comment-item {
  @apply p-4 border-l-2 border-transparent transition-colors;
}

.comment-item:hover {
  @apply border-l-blue-500 dark:border-l-blue-400 bg-gray-50 dark:bg-gray-800/50;
}

.comment-item--depth-0 {
  @apply ml-0;
}

.comment-item--depth-1 {
  @apply ml-8;
}

.comment-item--depth-2 {
  @apply ml-16;
}

.comment-item--depth-3 {
  @apply ml-24;
}

.comment-item--depth-4 {
  @apply ml-32;
}

.comment-item--depth-5 {
  @apply ml-40;
}

@media (max-width: 640px) {
  .comment-item--depth-1,
  .comment-item--depth-2,
  .comment-item--depth-3,
  .comment-item--depth-4,
  .comment-item--depth-5 {
    @apply ml-4;
  }
}

.comment-item__header {
  @apply flex gap-3 mb-3;
}

.comment-item__avatar {
  @apply flex-shrink-0;
}

.comment-item__avatar-circle {
  @apply w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-base;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.comment-item__meta {
  @apply flex-1;
}

.comment-item__author {
  @apply flex items-center gap-2 mb-1;
}

.comment-item__author-name {
  @apply font-semibold text-gray-900 dark:text-gray-100 text-sm;
}

.comment-item__badge {
  @apply px-2 py-0.5 rounded-full text-xs font-medium;
}

.comment-item__badge--user {
  @apply bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300;
}

.comment-item__badge--anonymous {
  @apply bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400;
}

.comment-item__date {
  @apply text-xs text-gray-500 dark:text-gray-400;
}

.comment-item__content {
  @apply ml-[calc(2.5rem+0.75rem)] mb-3 text-gray-700 dark:text-gray-300 text-sm leading-relaxed break-words;
}

.comment-item__content :deep(p) {
  @apply mb-2;
}

.comment-item__content :deep(p:last-child) {
  @apply mb-0;
}

.comment-item__content :deep(a) {
  @apply text-blue-600 dark:text-blue-400 underline hover:text-blue-700 dark:hover:text-blue-300;
}

.comment-item__content :deep(code) {
  @apply bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[0.8125rem] font-mono;
}

.comment-item__content :deep(pre) {
  @apply bg-gray-900 dark:bg-gray-950 text-gray-100 dark:text-gray-200 p-4 rounded-lg overflow-x-auto my-2;
}

.comment-item__content :deep(pre code) {
  @apply bg-transparent p-0;
}

.comment-item__actions {
  @apply ml-[calc(2.5rem+0.75rem)] flex gap-4;
}

.comment-item__action-button {
  @apply inline-flex items-center gap-1.5 px-3 py-1.5 text-[0.8125rem] font-medium text-gray-500 dark:text-gray-400 bg-transparent border-0 rounded-md cursor-pointer transition-all;
}

.comment-item__action-button:hover {
  @apply text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20;
}

.comment-item__action-icon {
  @apply w-4 h-4;
}
</style>
