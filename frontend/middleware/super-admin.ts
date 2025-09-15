/**
 * Super Admin middleware - protects routes that require super admin permissions only
 * Redirects non-super-admin users to dashboard with error message
 */
import { useAuth } from '~/interface/composables/useAuth';
import { ROLE } from '~/domain/types/permissions.types';

export default defineNuxtRouteMiddleware(() => {
  // Skip on server-side rendering to avoid hydration issues
  if (process.server) return;

  const { isAuthenticated, user, restoreSession } = useAuth();

  // Try to restore session first
  restoreSession();

  // First check if user is authenticated
  if (!isAuthenticated.value) {
    return navigateTo('/auth/login');
  }

  // Check if user has super admin role
  const currentUser = user.value;
  if (!currentUser || !currentUser.hasRole(ROLE.SUPER_ADMIN)) {
    // Redirect to dashboard with error message
    return navigateTo({
      path: '/dashboard',
      query: { error: 'No tienes permisos para acceder a esta sección' }
    });
  }
});