<template>
  <div class="comments-list">
    <div class="comments-list__header">
      <h2 class="comments-list__title">
        Comentarios
        <span v-if="commentCount > 0" class="comments-list__count">({{ commentCount }})</span>
      </h2>
    </div>

    <!-- Formulario principal (raíz) -->
    <CommentForm
      :post-slug="postSlug"
      @comment-created="handleCommentCreated"
    />

    <!-- Loading state -->
    <div v-if="loading && comments.length === 0" class="comments-list__loading">
      <div class="comments-list__skeleton" v-for="i in 3" :key="i">
        <div class="comments-list__skeleton-avatar"></div>
        <div class="comments-list__skeleton-content">
          <div class="comments-list__skeleton-line comments-list__skeleton-line--short"></div>
          <div class="comments-list__skeleton-line"></div>
          <div class="comments-list__skeleton-line"></div>
        </div>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="comments-list__error">
      <svg class="comments-list__error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="comments-list__error-text">{{ error }}</p>
      <button
        type="button"
        class="comments-list__retry-button"
        @click="loadComments"
      >
        Reintentar
      </button>
    </div>

    <!-- Árbol de comentarios -->
    <CommentTree
      v-else
      :comments="comments"
      :post-slug="postSlug"
      @reply-created="handleReplyCreated"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import CommentForm from './CommentForm.vue';
import CommentTree from './CommentTree.vue';
import { useComments } from '../../composables/useComments';

interface Props {
  postSlug: string;
}

const props = defineProps<Props>();

// Usar el composable
const {
  comments,
  commentCount,
  loading,
  error,
  loadComments,
  refreshComments,
} = useComments(props.postSlug);

// Methods
const handleCommentCreated = async () => {
  // Recargar comentarios después de crear uno nuevo
  await refreshComments();
};

const handleReplyCreated = async () => {
  // Recargar comentarios después de crear una respuesta
  await refreshComments();
};

// Lifecycle
onMounted(() => {
  loadComments();
});
</script>

<style scoped>
.comments-list {
  margin-top: 3rem;
  margin-bottom: 3rem;
}

.comments-list__header {
  margin-bottom: 1.5rem;
}

.comments-list__title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.comments-list__count {
  font-size: 1.25rem;
  color: #6b7280;
  font-weight: 400;
}

.comments-list__loading {
  padding: 2rem 0;
}

.comments-list__skeleton {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.comments-list__skeleton-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #e5e7eb;
  flex-shrink: 0;
}

.comments-list__skeleton-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.comments-list__skeleton-line {
  height: 0.75rem;
  background-color: #e5e7eb;
  border-radius: 0.25rem;
}

.comments-list__skeleton-line--short {
  width: 40%;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.comments-list__error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
}

.comments-list__error-icon {
  width: 3rem;
  height: 3rem;
  color: #ef4444;
  margin-bottom: 1rem;
}

.comments-list__error-text {
  font-size: 0.875rem;
  color: #991b1b;
  margin-bottom: 1rem;
  text-align: center;
}

.comments-list__retry-button {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #ffffff;
  background-color: #ef4444;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.comments-list__retry-button:hover {
  background-color: #dc2626;
}
</style>
