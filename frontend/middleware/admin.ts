/**
 * Admin middleware - protects routes that require admin or super admin permissions
 * Redirects non-admin users to dashboard with error message
 */
import { useAuthStore } from '~/interface/stores/auth.store';
import { ROLE } from '~/domain/types/permissions.types';

export default defineNuxtRouteMiddleware(() => {
  // Skip on server-side rendering to avoid hydration issues
  if (import.meta.server) return;

  const authStore = useAuthStore();

  // Try to restore session first
  authStore.restoreSession();

  // First check if user is authenticated
  if (!authStore.isAuthenticated) {
    return navigateTo('/auth/login');
  }

  // Check if user has admin role or higher
  const currentUser = authStore.currentUser;
  if (!currentUser || (!currentUser.hasRole(ROLE.ADMIN) && !currentUser.hasRole(ROLE.SUPER_ADMIN))) {
    // Redirect to dashboard with error message
    return navigateTo({
      path: '/dashboard',
      query: { error: 'No tienes permisos para acceder a esta sección' }
    });
  }
});