/**
 * Admin middleware - protects routes that require admin or super admin permissions
 * Redirects non-admin users to dashboard with error message
 */
import { useAuthStore } from '~/interface/stores/auth.store';
import { ROLE } from '~/domain/types/permissions.types';

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore();

  console.log({authStore});
  

  // Try to restore session first (await to ensure completion)
  await authStore.restoreSession();

  // First check if user is authenticated
  if (!authStore.isLoggedIn) {
    return navigateTo('/auth/login');
  }

  // Check if user has admin role or higher
  const currentUser = authStore.currentUser;
  console.log({currentUser});
  
  if (!currentUser || (!currentUser.hasRole(ROLE.ADMIN) && !currentUser.hasRole(ROLE.SUPER_ADMIN))) {
    // Show 404 instead of revealing route exists
    throw createError({
      statusCode: 404,
      statusMessage: 'Página no encontrada'
    });
  }
});