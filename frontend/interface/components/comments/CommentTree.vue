<template>
  <div class="comment-tree">
    <div v-for="node in comments" :key="node.comment.id" class="comment-tree__node">
      <CommentItem
        :comment="node.comment"
        :depth="depth"
        :max-depth="maxDepth"
        @reply-clicked="handleReplyClicked"
      />

      <!-- Formulario de respuesta -->
      <CommentReplyForm
        v-if="activeReplyId === node.comment.id"
        :post-slug="postSlug"
        :parent-id="node.comment.id"
        :parent-author-name="getAuthorName(node.comment)"
        @reply-created="handleReplyCreated"
        @cancel="handleReplyCancel"
      />

      <!-- Respuestas recursivas -->
      <CommentTree
        v-if="node.replies && node.replies.length > 0 && depth < maxDepth"
        :comments="node.replies"
        :depth="depth + 1"
        :max-depth="maxDepth"
        :post-slug="postSlug"
        @reply-clicked="handleReplyClicked"
        @reply-created="handleReplyCreated"
      />

      <!-- Mensaje cuando se alcanza profundidad máxima pero hay más respuestas -->
      <div
        v-if="node.replies && node.replies.length > 0 && depth >= maxDepth"
        class="comment-tree__depth-limit"
      >
        <svg class="comment-tree__depth-limit-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
        <span>Ver {{ node.replies.length }} respuesta{{ node.replies.length > 1 ? 's' : '' }} más</span>
      </div>
    </div>

    <div v-if="comments.length === 0 && depth === 0" class="comment-tree__empty">
      <svg class="comment-tree__empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
      </svg>
      <p class="comment-tree__empty-text">No hay comentarios aún</p>
      <p class="comment-tree__empty-subtext">Sé el primero en comentar</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { CommentTreeNode, CommentData } from '../../../domain/types/comment.types';
import CommentItem from './CommentItem.vue';
import CommentReplyForm from './CommentReplyForm.vue';

interface Props {
  comments: CommentTreeNode[];
  depth?: number;
  maxDepth?: number;
  postSlug: string;
}

interface Emits {
  (e: 'reply-clicked', commentId: string): void;
  (e: 'reply-created'): void;
}

const props = withDefaults(defineProps<Props>(), {
  depth: 0,
  maxDepth: 5,
});

const emit = defineEmits<Emits>();

const activeReplyId = ref<string | null>(null);

const handleReplyClicked = (commentId: string) => {
  if (activeReplyId.value === commentId) {
    activeReplyId.value = null;
  } else {
    activeReplyId.value = commentId;
  }
  emit('reply-clicked', commentId);
};

const handleReplyCreated = () => {
  activeReplyId.value = null;
  emit('reply-created');
};

const handleReplyCancel = () => {
  activeReplyId.value = null;
};

const getAuthorName = (comment: CommentData): string => {
  if (comment.authorType === 'user') {
    return comment.userName || 'Usuario';
  }
  return comment.anonymousName || 'Anónimo';
};
</script>

<style scoped>
.comment-tree {
  width: 100%;
}

.comment-tree__node {
  position: relative;
}

.comment-tree__depth-limit {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-left: calc(2rem * var(--depth, 1));
  padding: 0.75rem 1rem;
  background-color: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  color: #3b82f6;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.comment-tree__depth-limit:hover {
  background-color: #e5e7eb;
  border-color: #3b82f6;
}

.comment-tree__depth-limit-icon {
  width: 1.125rem;
  height: 1.125rem;
}

.comment-tree__empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
}

.comment-tree__empty-icon {
  width: 3rem;
  height: 3rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.comment-tree__empty-text {
  font-size: 1.125rem;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.comment-tree__empty-subtext {
  font-size: 0.875rem;
  color: #9ca3af;
}
</style>
