/**
 * Authentication middleware - protects routes that require authentication
 * Redirects unauthenticated users to login page
 */
import { useAuthStore } from '~/interface/stores/auth.store';

export default defineNuxtRouteMiddleware(async (to) => {
  // Skip on server-side rendering to avoid hydration issues
  if (import.meta.server) return;

  const authStore = useAuthStore();

  // Try to restore session first
  await authStore.restoreSession();

  // If user is not authenticated, redirect to login
  if (!authStore.isAuthenticated) {
    // Store the intended destination for redirect after login
    const redirectPath = to.fullPath;

    return navigateTo({
      path: '/auth/login',
      query: { redirect: redirectPath }
    });
  }
});