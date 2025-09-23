<template>
  <div
    v-if="visible"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
    @click="onBackdropClick"
  >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-sm mx-4 text-center"
          @click.stop
        >
          <!-- Loading Spinner -->
          <div class="flex justify-center mb-4">
            <div class="relative">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
              <div class="absolute inset-0 rounded-full border-2 border-gray-200 dark:border-gray-600"></div>
            </div>
          </div>

          <!-- Loading Message -->
          <div v-if="message" class="text-gray-700 dark:text-gray-300 mb-2">
            {{ message }}
          </div>

          <!-- Default Loading Text -->
          <div v-else class="text-gray-700 dark:text-gray-300 mb-2">
            Cargando...
          </div>

          <!-- Cancel Button (if cancellable) -->
          <button
            v-if="cancellable"
            @click="onCancel"
            class="mt-4 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
          >
            Cancelar
          </button>
        </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useLoadingStore } from '../../stores/loading.store';

interface Props {
  visible?: boolean;
  message?: string | null;
  cancellable?: boolean;
  backdropClosable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  visible: undefined,
  message: null,
  cancellable: false,
  backdropClosable: false
});

const emit = defineEmits<{
  cancel: [];
  backdropClick: [];
}>();

const loadingStore = useLoadingStore();

// Use store visibility if not provided as prop
const visible = computed(() => {
  return props.visible !== undefined ? props.visible : loadingStore.isLoading;
});

// Use store message if not provided as prop
const message = computed(() => {
  return props.message !== undefined ? props.message : loadingStore.message;
});

const onCancel = () => {
  emit('cancel');
};

const onBackdropClick = () => {
  if (props.backdropClosable) {
    emit('backdropClick');
  }
};
</script>

<style scoped>
/* Custom loading animation */
@keyframes pulse-subtle {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

.loading-overlay-enter-active,
.loading-overlay-leave-active {
  transition: all 0.3s ease;
}

.loading-overlay-enter-from,
.loading-overlay-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>