/**
 * Super Admin middleware - protects routes that require super admin permissions only
 * Redirects non-super-admin users to dashboard with error message
 */
import { useAuthStore } from '~/interface/stores/auth.store';
import { useLoadingStore } from '~/interface/stores/loading.store';
import { ROLE } from '~/domain/types/permissions.types';

export default defineNuxtRouteMiddleware(async () => {
  // Skip middleware on server-side rendering to avoid hydration issues
  if (import.meta.server) return;

  const authStore = useAuthStore();
  const loadingStore = useLoadingStore();

  // Show loading during middleware validation
  loadingStore.show('Cargando...');

  try {
    // Try to restore session first (await to ensure completion)
    await authStore.restoreSession();

    // First check if user is authenticated
    if (!authStore.isLoggedIn) {
      loadingStore.hide();
      return navigateTo('/auth/login');
    }

    // Check if user has super admin role
    const currentUser = authStore.currentUser;
    if (!currentUser || !currentUser.hasRole(ROLE.SUPER_ADMIN)) {
      loadingStore.hide();
      // Show 404 instead of revealing route exists
      throw createError({
        statusCode: 404,
        statusMessage: 'Página no encontrada'
      });
    }

    // All checks passed, stop loading
    loadingStore.hide();
  } catch (error) {
    // Clean up loading on error
    loadingStore.hide();
    throw error;
  }
});