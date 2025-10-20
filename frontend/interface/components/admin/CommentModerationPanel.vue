<template>
  <div class="moderation-panel">
    <div class="moderation-panel__header">
      <h1 class="moderation-panel__title">Moderación de Comentarios</h1>

      <div class="moderation-panel__filters">
        <button
          v-for="filter in filters"
          :key="filter.value"
          type="button"
          class="moderation-panel__filter-button"
          :class="{ 'moderation-panel__filter-button--active': activeFilter === filter.value }"
          @click="activeFilter = filter.value"
        >
          {{ filter.label }}
          <span
            v-if="filter.count !== undefined"
            class="moderation-panel__filter-count"
          >
            {{ filter.count }}
          </span>
        </button>
      </div>
    </div>

    <div class="moderation-panel__search">
      <svg class="moderation-panel__search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <input
        v-model="searchQuery"
        type="text"
        class="moderation-panel__search-input"
        placeholder="Buscar por contenido o autor..."
      />
      <button
        v-if="searchQuery"
        type="button"
        class="moderation-panel__search-clear"
        @click="searchQuery = ''"
      >
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <div v-if="loading" class="moderation-panel__loading">
      <div class="moderation-panel__spinner"></div>
      <p>Cargando comentarios...</p>
    </div>

    <div v-else-if="error" class="moderation-panel__error">
      <svg class="moderation-panel__error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p>{{ error }}</p>
      <button
        type="button"
        class="moderation-panel__retry-button"
        @click="loadPendingComments"
      >
        Reintentar
      </button>
    </div>

    <div v-else-if="filteredComments.length === 0" class="moderation-panel__empty">
      <svg class="moderation-panel__empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
      <p>No hay comentarios {{ activeFilter === 'all' ? '' : activeFilter }}</p>
    </div>

    <div v-else class="moderation-panel__list">
      <CommentModerationItem
        v-for="comment in paginatedComments"
        :key="comment.id"
        :comment="comment"
        :selected="selectedIds.includes(comment.id)"
        @action-performed="handleActionPerformed"
        @selection-changed="handleSelectionChanged"
      />
    </div>

    <div v-if="totalPages > 1" class="moderation-panel__pagination">
      <button
        type="button"
        class="moderation-panel__pagination-button"
        :disabled="currentPage === 1"
        @click="currentPage--"
      >
        Anterior
      </button>

      <span class="moderation-panel__pagination-info">
        Página {{ currentPage }} de {{ totalPages }}
      </span>

      <button
        type="button"
        class="moderation-panel__pagination-button"
        :disabled="currentPage === totalPages"
        @click="currentPage++"
      >
        Siguiente
      </button>
    </div>

    <CommentBulkActions
      :selected-count="selectedIds.length"
      :selected-ids="selectedIds"
      @bulk-approve="handleBulkApprove"
      @bulk-reject="handleBulkReject"
      @bulk-delete="handleBulkDelete"
      @clear-selection="clearSelection"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import type { CommentData } from '../../../domain/types/comment.types';
import CommentModerationItem from './CommentModerationItem.vue';
import CommentBulkActions from './CommentBulkActions.vue';
import { useCommentModeration } from '../../composables/useCommentModeration';

// Usar composable
const {
  pendingComments,
  loading,
  error,
  selectedIds,
  loadPendingComments,
  approveComment,
  rejectComment,
  markAsSpam,
  deleteComment,
  bulkApprove,
  bulkReject,
  bulkDelete,
  clearSelection,
} = useCommentModeration();

// State local (filtros y paginación)
const activeFilter = ref<string>('pending');
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 20;

// Convertir Comment entities a CommentData para el componente
const comments = computed(() => {
  return pendingComments.value.map(c => c.toData());
});

// Computed
const filters = computed(() => {
  const pendingCount = comments.value.filter((c) => c.status === 'pending').length;
  const approvedCount = comments.value.filter((c) => c.status === 'approved').length;
  const rejectedCount = comments.value.filter((c) => c.status === 'rejected').length;
  const spamCount = comments.value.filter((c) => c.status === 'spam').length;

  return [
    { label: 'Pendientes', value: 'pending', count: pendingCount },
    { label: 'Aprobados', value: 'approved', count: approvedCount },
    { label: 'Rechazados', value: 'rejected', count: rejectedCount },
    { label: 'Spam', value: 'spam', count: spamCount },
    { label: 'Todos', value: 'all', count: comments.value.length },
  ];
});

const filteredComments = computed(() => {
  let filtered = comments.value;

  // Filtrar por estado
  if (activeFilter.value !== 'all') {
    filtered = filtered.filter((c) => c.status === activeFilter.value);
  }

  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter((c) => {
      const content = c.content.toLowerCase();
      const authorName = c.authorType === 'user'
        ? (c.userName || '').toLowerCase()
        : (c.anonymousName || '').toLowerCase();
      const authorEmail = c.authorType === 'anonymous'
        ? (c.anonymousEmail || '').toLowerCase()
        : '';

      return content.includes(query) || authorName.includes(query) || authorEmail.includes(query);
    });
  }

  return filtered;
});

const totalPages = computed(() => {
  return Math.ceil(filteredComments.value.length / itemsPerPage);
});

const paginatedComments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredComments.value.slice(start, end);
});

// Methods
const handleActionPerformed = async (
  action: 'approve' | 'reject' | 'spam' | 'delete',
  commentId: string
) => {
  try {
    switch (action) {
      case 'approve':
        await approveComment(commentId);
        break;
      case 'reject':
        await rejectComment(commentId);
        break;
      case 'spam':
        await markAsSpam(commentId);
        break;
      case 'delete':
        await deleteComment(commentId);
        break;
    }
  } catch (err: any) {
    // Error ya manejado por el composable
    console.error('Error in action:', err);
  }
};

const handleSelectionChanged = (commentId: string, selected: boolean) => {
  if (selected) {
    if (!selectedIds.value.includes(commentId)) {
      selectedIds.value.push(commentId);
    }
  } else {
    const index = selectedIds.value.indexOf(commentId);
    if (index > -1) {
      selectedIds.value.splice(index, 1);
    }
  }
};

const handleBulkApprove = async (ids: string[]) => {
  await bulkApprove(ids);
};

const handleBulkReject = async (ids: string[]) => {
  await bulkReject(ids);
};

const handleBulkDelete = async (ids: string[]) => {
  await bulkDelete(ids);
};

// Watchers
watch(activeFilter, () => {
  currentPage.value = 1;
  clearSelection();
});

watch(searchQuery, () => {
  currentPage.value = 1;
});
</script>

<style scoped>
.moderation-panel {
  padding: 2rem;
  max-width: 1280px;
  margin: 0 auto;
  padding-bottom: 6rem; /* Space for bulk actions bar */
}

.moderation-panel__header {
  margin-bottom: 2rem;
}

.moderation-panel__title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 1.5rem;
}

.moderation-panel__filters {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.moderation-panel__filter-button {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.moderation-panel__filter-button:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.moderation-panel__filter-button--active {
  background-color: #3b82f6;
  color: #ffffff;
  border-color: #3b82f6;
}

.moderation-panel__filter-count {
  padding: 0.125rem 0.5rem;
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.moderation-panel__filter-button--active .moderation-panel__filter-count {
  background-color: rgba(255, 255, 255, 0.3);
}

.moderation-panel__search {
  position: relative;
  margin-bottom: 1.5rem;
}

.moderation-panel__search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.25rem;
  height: 1.25rem;
  color: #6b7280;
}

.moderation-panel__search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 3rem;
  font-size: 0.875rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  transition: border-color 0.2s;
}

.moderation-panel__search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.moderation-panel__search-clear {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  padding: 0.25rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
}

.moderation-panel__search-clear svg {
  width: 1.25rem;
  height: 1.25rem;
}

.moderation-panel__loading,
.moderation-panel__error,
.moderation-panel__empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.moderation-panel__spinner {
  width: 3rem;
  height: 3rem;
  border: 4px solid #e5e7eb;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.moderation-panel__error-icon,
.moderation-panel__empty-icon {
  width: 4rem;
  height: 4rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.moderation-panel__retry-button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #ffffff;
  background-color: #3b82f6;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.moderation-panel__retry-button:hover {
  background-color: #2563eb;
}

.moderation-panel__list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.moderation-panel__pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.moderation-panel__pagination-button {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
}

.moderation-panel__pagination-button:hover:not(:disabled) {
  background-color: #f9fafb;
  border-color: #3b82f6;
}

.moderation-panel__pagination-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.moderation-panel__pagination-info {
  font-size: 0.875rem;
  color: #6b7280;
}
</style>
