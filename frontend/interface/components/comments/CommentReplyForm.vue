<template>
  <div class="comment-reply-form">
    <div class="comment-reply-form__header">
      <span class="comment-reply-form__replying-to">
        Respondiendo a <strong>@{{ parentAuthorName }}</strong>
      </span>
      <button
        type="button"
        class="comment-reply-form__close"
        @click="handleCancel"
        :disabled="isSubmitting"
      >
        <svg class="comment-reply-form__close-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <CommentForm
      :post-slug="postSlug"
      :parent-id="parentId"
      title=""
      :show-cancel="true"
      @comment-created="handleCommentCreated"
      @cancel="handleCancel"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import CommentForm from './CommentForm.vue';

interface Props {
  postSlug: string;
  parentId: string;
  parentAuthorName: string;
}

interface Emits {
  (e: 'reply-created'): void;
  (e: 'cancel'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const isSubmitting = ref(false);

const handleCommentCreated = () => {
  emit('reply-created');
};

const handleCancel = () => {
  emit('cancel');
};
</script>

<style scoped>
.comment-reply-form {
  margin: 1rem 0;
  padding: 1rem;
  background-color: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.comment-reply-form__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.comment-reply-form__replying-to {
  font-size: 0.875rem;
  color: #6b7280;
}

.comment-reply-form__replying-to strong {
  color: #3b82f6;
  font-weight: 600;
}

.comment-reply-form__close {
  padding: 0.25rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  transition: color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.comment-reply-form__close:hover:not(:disabled) {
  color: #ef4444;
}

.comment-reply-form__close:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.comment-reply-form__close-icon {
  width: 1.25rem;
  height: 1.25rem;
}
</style>
