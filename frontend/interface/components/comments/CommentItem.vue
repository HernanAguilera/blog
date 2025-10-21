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
  padding: 1rem;
  border-left: 2px solid transparent;
  transition: border-color 0.2s;
}

.comment-item:hover {
  border-left-color: #3b82f6;
  background-color: #f9fafb;
}

.comment-item--depth-0 {
  margin-left: 0;
}

.comment-item--depth-1 {
  margin-left: 2rem;
}

.comment-item--depth-2 {
  margin-left: 4rem;
}

.comment-item--depth-3 {
  margin-left: 6rem;
}

.comment-item--depth-4 {
  margin-left: 8rem;
}

.comment-item--depth-5 {
  margin-left: 10rem;
}

@media (max-width: 640px) {
  .comment-item--depth-1,
  .comment-item--depth-2,
  .comment-item--depth-3,
  .comment-item--depth-4,
  .comment-item--depth-5 {
    margin-left: 1rem;
  }
}

.comment-item__header {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.comment-item__avatar {
  flex-shrink: 0;
}

.comment-item__avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-weight: 600;
  font-size: 1rem;
}

.comment-item__meta {
  flex: 1;
}

.comment-item__author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
}

.comment-item__author-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}

.comment-item__badge {
  padding: 0.125rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.comment-item__badge--user {
  background-color: #dbeafe;
  color: #1e40af;
}

.comment-item__badge--anonymous {
  background-color: #f3f4f6;
  color: #6b7280;
}

.comment-item__date {
  font-size: 0.75rem;
  color: #6b7280;
}

.comment-item__content {
  margin-left: calc(40px + 0.75rem);
  margin-bottom: 0.75rem;
  color: #374151;
  font-size: 0.875rem;
  line-height: 1.5;
  word-wrap: break-word;
}

.comment-item__content :deep(p) {
  margin-bottom: 0.5rem;
}

.comment-item__content :deep(p:last-child) {
  margin-bottom: 0;
}

.comment-item__content :deep(a) {
  color: #3b82f6;
  text-decoration: underline;
}

.comment-item__content :deep(code) {
  background-color: #f3f4f6;
  padding: 0.125rem 0.375rem;
  border-radius: 0.25rem;
  font-size: 0.8125rem;
  font-family: monospace;
}

.comment-item__content :deep(pre) {
  background-color: #1f2937;
  color: #f3f4f6;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 0.5rem 0;
}

.comment-item__content :deep(pre code) {
  background-color: transparent;
  padding: 0;
  color: inherit;
}

.comment-item__actions {
  margin-left: calc(40px + 0.75rem);
  display: flex;
  gap: 1rem;
}

.comment-item__action-button {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  font-size: 0.8125rem;
  font-weight: 500;
  color: #6b7280;
  background: none;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
}

.comment-item__action-button:hover {
  color: #3b82f6;
  background-color: #eff6ff;
}

.comment-item__action-icon {
  width: 1rem;
  height: 1rem;
}
</style>
