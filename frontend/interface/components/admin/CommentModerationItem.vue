<template>
  <div class="moderation-item" :class="{ 'moderation-item--selected': selected }">
    <div class="moderation-item__checkbox">
      <input
        type="checkbox"
        :checked="selected"
        @change="handleSelectionChange"
        class="moderation-item__checkbox-input"
      />
    </div>

    <div class="moderation-item__content">
      <div class="moderation-item__header">
        <div class="moderation-item__author">
          <span class="moderation-item__author-name">{{ authorName }}</span>
          <span
            class="moderation-item__badge"
            :class="{
              'moderation-item__badge--user': isUserComment,
              'moderation-item__badge--anonymous': !isUserComment,
            }"
          >
            {{ isUserComment ? 'Usuario registrado' : 'Anónimo' }}
          </span>
          <span v-if="!isUserComment && comment.anonymousEmail" class="moderation-item__email">
            {{ comment.anonymousEmail }}
          </span>
        </div>

        <div class="moderation-item__meta">
          <span class="moderation-item__date">{{ formattedDate }}</span>
          <span class="moderation-item__status-badge" :class="`moderation-item__status-badge--${comment.status}`">
            {{ statusLabel }}
          </span>
        </div>
      </div>

      <div class="moderation-item__text">
        {{ comment.content }}
      </div>

      <div v-if="comment.parentId" class="moderation-item__context">
        <svg class="moderation-item__context-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
        </svg>
        <span>Respuesta a otro comentario</span>
      </div>

      <div class="moderation-item__actions">
        <button
          type="button"
          class="moderation-item__action-btn moderation-item__action-btn--approve"
          :disabled="isProcessing || comment.status === 'approved'"
          @click="handleApprove"
          title="Aprobar comentario"
        >
          <svg class="moderation-item__action-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Aprobar
        </button>

        <button
          type="button"
          class="moderation-item__action-btn moderation-item__action-btn--reject"
          :disabled="isProcessing || comment.status === 'rejected'"
          @click="handleReject"
          title="Rechazar comentario"
        >
          <svg class="moderation-item__action-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Rechazar
        </button>

        <button
          type="button"
          class="moderation-item__action-btn moderation-item__action-btn--spam"
          :disabled="isProcessing || comment.status === 'spam'"
          @click="handleMarkSpam"
          title="Marcar como spam"
        >
          <svg class="moderation-item__action-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          Spam
        </button>

        <button
          type="button"
          class="moderation-item__action-btn moderation-item__action-btn--delete"
          :disabled="isProcessing"
          @click="handleDelete"
          title="Eliminar comentario"
        >
          <svg class="moderation-item__action-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Eliminar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import type { CommentData } from '../../../domain/types/comment.types';

interface Props {
  comment: CommentData;
  selected: boolean;
}

interface Emits {
  (e: 'action-performed', action: 'approve' | 'reject' | 'spam' | 'delete', commentId: string): void;
  (e: 'selection-changed', commentId: string, selected: boolean): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const isProcessing = ref(false);

// Computed
const isUserComment = computed(() => props.comment.authorType === 'user');

const authorName = computed(() => {
  if (isUserComment.value) {
    return props.comment.userName || 'Usuario';
  }
  return props.comment.anonymousName || 'Anónimo';
});

const formattedDate = computed(() => {
  const date = new Date(props.comment.createdAt);
  return date.toLocaleString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
});

const statusLabel = computed(() => {
  const labels: Record<string, string> = {
    pending: 'Pendiente',
    approved: 'Aprobado',
    rejected: 'Rechazado',
    spam: 'Spam',
  };
  return labels[props.comment.status] || props.comment.status;
});

// Methods
const handleSelectionChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  emit('selection-changed', props.comment.id, target.checked);
};

const handleApprove = async () => {
  if (confirm('¿Aprobar este comentario?')) {
    isProcessing.value = true;
    emit('action-performed', 'approve', props.comment.id);
    // isProcessing se resetea cuando el componente padre actualice los datos
  }
};

const handleReject = async () => {
  if (confirm('¿Rechazar este comentario?')) {
    isProcessing.value = true;
    emit('action-performed', 'reject', props.comment.id);
  }
};

const handleMarkSpam = async () => {
  if (confirm('¿Marcar este comentario como spam?')) {
    isProcessing.value = true;
    emit('action-performed', 'spam', props.comment.id);
  }
};

const handleDelete = async () => {
  if (confirm('¿Eliminar permanentemente este comentario? Esta acción no se puede deshacer.')) {
    isProcessing.value = true;
    emit('action-performed', 'delete', props.comment.id);
  }
};
</script>

<style scoped>
.moderation-item {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  transition: all 0.2s;
}

.moderation-item:hover {
  border-color: #3b82f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.moderation-item--selected {
  background-color: #eff6ff;
  border-color: #3b82f6;
}

.moderation-item__checkbox {
  flex-shrink: 0;
}

.moderation-item__checkbox-input {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.moderation-item__content {
  flex: 1;
}

.moderation-item__header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 0.75rem;
  gap: 1rem;
}

.moderation-item__author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.moderation-item__author-name {
  font-weight: 600;
  color: #111827;
}

.moderation-item__badge {
  padding: 0.125rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.moderation-item__badge--user {
  background-color: #dbeafe;
  color: #1e40af;
}

.moderation-item__badge--anonymous {
  background-color: #f3f4f6;
  color: #6b7280;
}

.moderation-item__email {
  font-size: 0.875rem;
  color: #6b7280;
}

.moderation-item__meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-shrink: 0;
}

.moderation-item__date {
  font-size: 0.75rem;
  color: #6b7280;
}

.moderation-item__status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.moderation-item__status-badge--pending {
  background-color: #fef3c7;
  color: #92400e;
}

.moderation-item__status-badge--approved {
  background-color: #d1fae5;
  color: #065f46;
}

.moderation-item__status-badge--rejected {
  background-color: #fee2e2;
  color: #991b1b;
}

.moderation-item__status-badge--spam {
  background-color: #fce7f3;
  color: #831843;
}

.moderation-item__text {
  margin-bottom: 1rem;
  color: #374151;
  line-height: 1.5;
  word-wrap: break-word;
}

.moderation-item__context {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background-color: #f3f4f6;
  border-radius: 6px;
  font-size: 0.8125rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.moderation-item__context-icon {
  width: 1rem;
  height: 1rem;
}

.moderation-item__actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.moderation-item__action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 1rem;
  font-size: 0.8125rem;
  font-weight: 500;
  border: 1px solid transparent;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.moderation-item__action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.moderation-item__action-icon {
  width: 1rem;
  height: 1rem;
}

.moderation-item__action-btn--approve {
  background-color: #d1fae5;
  color: #065f46;
  border-color: #a7f3d0;
}

.moderation-item__action-btn--approve:hover:not(:disabled) {
  background-color: #a7f3d0;
}

.moderation-item__action-btn--reject {
  background-color: #fee2e2;
  color: #991b1b;
  border-color: #fecaca;
}

.moderation-item__action-btn--reject:hover:not(:disabled) {
  background-color: #fecaca;
}

.moderation-item__action-btn--spam {
  background-color: #fef3c7;
  color: #92400e;
  border-color: #fde68a;
}

.moderation-item__action-btn--spam:hover:not(:disabled) {
  background-color: #fde68a;
}

.moderation-item__action-btn--delete {
  background-color: #f3f4f6;
  color: #374151;
  border-color: #e5e7eb;
}

.moderation-item__action-btn--delete:hover:not(:disabled) {
  background-color: #e5e7eb;
  color: #ef4444;
}
</style>
