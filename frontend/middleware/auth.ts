/**
 * Authentication middleware - protects routes that require authentication
 * Redirects unauthenticated users to login page
 */
import { useAuth } from '~/interface/composables/useAuth';

export default defineNuxtRouteMiddleware((to) => {
  // Skip on server-side rendering to avoid hydration issues
  if (process.server) return;

  const { isAuthenticated, restoreSession } = useAuth();

  // Try to restore session first
  restoreSession();

  // If user is not authenticated, redirect to login
  if (!isAuthenticated.value) {
    // Store the intended destination for redirect after login
    const redirectPath = to.fullPath;

    return navigateTo({
      path: '/auth/login',
      query: { redirect: redirectPath }
    });
  }
});