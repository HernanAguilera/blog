<template>
  <transition name="slide-up">
    <div v-if="selectedCount > 0" class="bulk-actions">
      <div class="bulk-actions__container">
        <div class="bulk-actions__info">
          <span class="bulk-actions__count">{{ selectedCount }}</span>
          <span class="bulk-actions__text">
            comentario{{ selectedCount > 1 ? 's' : '' }} seleccionado{{ selectedCount > 1 ? 's' : '' }}
          </span>
        </div>

        <div class="bulk-actions__buttons">
          <button
            type="button"
            class="bulk-actions__button bulk-actions__button--approve"
            :disabled="isProcessing"
            @click="handleBulkApprove"
          >
            <svg class="bulk-actions__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Aprobar todos
          </button>

          <button
            type="button"
            class="bulk-actions__button bulk-actions__button--reject"
            :disabled="isProcessing"
            @click="handleBulkReject"
          >
            <svg class="bulk-actions__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Rechazar todos
          </button>

          <button
            type="button"
            class="bulk-actions__button bulk-actions__button--delete"
            :disabled="isProcessing"
            @click="handleBulkDelete"
          >
            <svg class="bulk-actions__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Eliminar todos
          </button>

          <button
            type="button"
            class="bulk-actions__button bulk-actions__button--cancel"
            :disabled="isProcessing"
            @click="handleClearSelection"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  selectedCount: number;
  selectedIds: string[];
}

interface Emits {
  (e: 'bulk-approve' | 'bulk-reject' | 'bulk-delete', ids: string[]): void;
  (e: 'clear-selection'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const isProcessing = ref(false);

const handleBulkApprove = () => {
  if (confirm(`¿Aprobar ${props.selectedCount} comentario${props.selectedCount > 1 ? 's' : ''}?`)) {
    isProcessing.value = true;
    emit('bulk-approve', props.selectedIds);
  }
};

const handleBulkReject = () => {
  if (confirm(`¿Rechazar ${props.selectedCount} comentario${props.selectedCount > 1 ? 's' : ''}?`)) {
    isProcessing.value = true;
    emit('bulk-reject', props.selectedIds);
  }
};

const handleBulkDelete = () => {
  if (
    confirm(
      `¿Eliminar permanentemente ${props.selectedCount} comentario${props.selectedCount > 1 ? 's' : ''}? Esta acción no se puede deshacer.`
    )
  ) {
    isProcessing.value = true;
    emit('bulk-delete', props.selectedIds);
  }
};

const handleClearSelection = () => {
  emit('clear-selection');
};
</script>

<style scoped>
.bulk-actions {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 40;
  background-color: #1f2937;
  border-top: 1px solid #374151;
  box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
}

.bulk-actions__container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
}

@media (max-width: 768px) {
  .bulk-actions__container {
    flex-direction: column;
    align-items: stretch;
  }
}

.bulk-actions__info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.bulk-actions__count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2rem;
  height: 2rem;
  padding: 0 0.5rem;
  background-color: #3b82f6;
  color: #ffffff;
  border-radius: 9999px;
  font-weight: 700;
  font-size: 0.875rem;
}

.bulk-actions__text {
  color: #f3f4f6;
  font-size: 0.875rem;
  font-weight: 500;
}

.bulk-actions__buttons {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

@media (max-width: 768px) {
  .bulk-actions__buttons {
    flex-direction: column;
  }
}

.bulk-actions__button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  font-size: 0.875rem;
  font-weight: 500;
  border: 1px solid transparent;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.bulk-actions__button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.bulk-actions__icon {
  width: 1.125rem;
  height: 1.125rem;
}

.bulk-actions__button--approve {
  background-color: #10b981;
  color: #ffffff;
}

.bulk-actions__button--approve:hover:not(:disabled) {
  background-color: #059669;
}

.bulk-actions__button--reject {
  background-color: #ef4444;
  color: #ffffff;
}

.bulk-actions__button--reject:hover:not(:disabled) {
  background-color: #dc2626;
}

.bulk-actions__button--delete {
  background-color: #6b7280;
  color: #ffffff;
}

.bulk-actions__button--delete:hover:not(:disabled) {
  background-color: #4b5563;
}

.bulk-actions__button--cancel {
  background-color: transparent;
  color: #f3f4f6;
  border-color: #4b5563;
}

.bulk-actions__button--cancel:hover:not(:disabled) {
  background-color: #374151;
}

/* Transition animations */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease-out;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

.slide-up-enter-to,
.slide-up-leave-from {
  transform: translateY(0);
}
</style>
