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
  @apply my-4 p-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg;
}

.comment-reply-form__header {
  @apply flex justify-between items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700;
}

.comment-reply-form__replying-to {
  @apply text-sm text-gray-600 dark:text-gray-400;
}

.comment-reply-form__replying-to strong {
  @apply text-blue-600 dark:text-blue-400 font-semibold;
}

.comment-reply-form__close {
  @apply p-1 bg-transparent border-0 cursor-pointer text-gray-500 dark:text-gray-400 transition-colors flex items-center justify-center;
}

.comment-reply-form__close:hover:not(:disabled) {
  @apply text-red-500 dark:text-red-400;
}

.comment-reply-form__close:disabled {
  @apply opacity-50 cursor-not-allowed;
}

.comment-reply-form__close-icon {
  @apply w-5 h-5;
}
</style>
