import { defineStore } from 'pinia';

interface LoadingState {
    isLoading: boolean;
    message: string | null;
}

export const useLoadingStore = defineStore('loading', {
    state: (): LoadingState => ({
        isLoading: false,
        message: null
    }),

    actions: {
        show(message?: string) {
            this.isLoading = true;
            this.message = message || null;
        },

        hide() {
            this.isLoading = false;
            this.message = null;
        }
    }
});