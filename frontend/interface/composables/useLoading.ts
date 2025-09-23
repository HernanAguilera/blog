import { computed, readonly } from 'vue';
import { useLoadingStore } from '../stores/loading.store';

/**
 * Simple loading composable - provides direct access to the loading store
 * @param message - Optional default message to show
 */
export function useLoading(message?: string) {
  const loadingStore = useLoadingStore();

  const isLoading = computed(() => loadingStore.isLoading);

  const show = (msg?: string) => {
    loadingStore.show(msg || message);
  };

  const hide = () => {
    loadingStore.hide();
  };

  /**
   * Execute an async function with loading state
   */
  const withLoading = async <T>(asyncFn: () => Promise<T>, msg?: string): Promise<T> => {
    try {
      show(msg);
      const result = await asyncFn();
      return result;
    } finally {
      hide();
    }
  };

  return {
    isLoading: readonly(isLoading),
    show,
    hide,
    withLoading
  };
}