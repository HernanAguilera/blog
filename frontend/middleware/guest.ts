/**
 * Guest middleware - protects routes that should only be accessible to unauthenticated users
 * Redirects authenticated users to dashboard or intended destination
 */
import { useAuthStore } from '~/interface/stores/auth.store';
import { useLoadingStore } from '~/interface/stores/loading.store';

export default defineNuxtRouteMiddleware(async (to) => {
  // Skip middleware on server-side rendering to avoid hydration issues
  if (import.meta.server) return;

  const authStore = useAuthStore();
  const loadingStore = useLoadingStore();

  // Show brief loading during middleware validation
  loadingStore.show('Cargando...');

  try {
    // Try to restore session first (async)
    await authStore.restoreSession();

    // If user is authenticated, redirect away from guest-only pages
    if (authStore.isLoggedIn) {
      loadingStore.hide();
      // Check if there's a redirect parameter for where they were trying to go
      const redirectTo = (to.query.redirect as string) || '/profile';

      return navigateTo(redirectTo);
    }

    // User is not authenticated, stop loading and allow access
    loadingStore.hide();
  } catch (error) {
    // Clean up loading on error
    loadingStore.hide();
    throw error;
  }
});