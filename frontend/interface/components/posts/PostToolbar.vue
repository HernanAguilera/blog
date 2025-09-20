<template>
  <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <!-- Left side: Status and actions -->
    <div class="flex items-center space-x-4">
      <PostStatusBadge :status="currentStatus" />

      <div class="flex items-center space-x-2">
        <!-- Save button -->
        <button
          :disabled="isSaving || !hasChanges"
          type="button"
          class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="$emit('save')"
        >
          <svg
            v-if="isSaving"
            class="animate-spin -ml-1 mr-2 h-3 w-3 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
          {{ isSaving ? 'Guardando...' : 'Guardar' }}
        </button>

        <!-- Preview button -->
        <button
          :disabled="isGeneratingPreview"
          type="button"
          class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="$emit('preview')"
        >
          <svg
            v-if="isGeneratingPreview"
            class="animate-spin -ml-1 mr-2 h-3 w-3 text-gray-500 dark:text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
          <svg
            v-else
            class="-ml-1 mr-2 h-3 w-3 text-gray-500 dark:text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
            />
          </svg>
          {{ isGeneratingPreview ? 'Generando...' : 'Vista previa' }}
        </button>
      </div>
    </div>

    <!-- Center: Auto-save status -->
    <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
      <div v-if="autoSaveEnabled" class="flex items-center space-x-1">
        <div
          :class="[
            'w-2 h-2 rounded-full',
            hasUnsavedChanges ? 'bg-yellow-400' : 'bg-green-400'
          ]"
        />
        <span v-if="hasUnsavedChanges">
          Cambios sin guardar
        </span>
        <span v-else-if="lastSaved">
          Guardado {{ formatLastSaved }}
        </span>
        <span v-else>
          Auto-guardado activado
        </span>
      </div>
    </div>

    <!-- Right side: Status actions -->
    <div class="flex items-center space-x-2">
      <!-- Status change dropdown -->
      <div v-if="availableTransitions.length > 0" class="relative">
        <button
          type="button"
          class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          @click="showStatusMenu = !showStatusMenu"
        >
          Cambiar estado
          <svg
            class="ml-1 h-3 w-3"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>

        <!-- Dropdown menu -->
        <div
          v-if="showStatusMenu"
          class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 dark:ring-gray-600 z-10"
          @click.away="showStatusMenu = false"
        >
          <div class="py-1">
            <button
              v-for="transition in availableTransitions"
              :key="transition"
              class="block w-full text-left px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
              @click="handleStatusChange(transition)"
            >
              {{ getStatusLabel(transition) }}
            </button>
          </div>
        </div>
      </div>

      <!-- Settings button -->
      <button
        type="button"
        class="inline-flex items-center p-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        @click="$emit('settings')"
      >
        <svg
          class="h-4 w-4"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import PostStatusBadge from './PostStatusBadge.vue';

interface Props {
  currentStatus: string;
  availableTransitions: string[];
  isSaving: boolean;
  isGeneratingPreview: boolean;
  hasChanges: boolean;
  autoSaveEnabled: boolean;
  hasUnsavedChanges: boolean;
  lastSaved: Date | null;
}

interface Emits {
  (e: 'save'): void;
  (e: 'preview'): void;
  (e: 'settings'): void;
  (e: 'status-change', status: string): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const showStatusMenu = ref(false);

const formatLastSaved = computed(() => {
  if (!props.lastSaved) return '';

  const now = new Date();
  const diffMs = now.getTime() - props.lastSaved.getTime();
  const diffMinutes = Math.floor(diffMs / 60000);

  if (diffMinutes < 1) return 'hace unos segundos';
  if (diffMinutes === 1) return 'hace 1 minuto';
  if (diffMinutes < 60) return `hace ${diffMinutes} minutos`;

  const diffHours = Math.floor(diffMinutes / 60);
  if (diffHours === 1) return 'hace 1 hora';
  if (diffHours < 24) return `hace ${diffHours} horas`;

  return props.lastSaved.toLocaleDateString();
});

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    draft: 'Borrador',
    scheduled: 'Programar',
    published: 'Publicar',
    archived: 'Archivar'
  };

  return labels[status] || status;
};

const handleStatusChange = (status: string) => {
  showStatusMenu.value = false;
  emit('status-change', status);
};

// Click away directive would be handled by a custom directive or composable
</script>