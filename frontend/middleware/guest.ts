/**
 * Guest middleware - protects routes that should only be accessible to unauthenticated users
 * Redirects authenticated users to dashboard or intended destination
 */
import { useAuth } from '~/interface/composables/useAuth';

export default defineNuxtRouteMiddleware((to) => {
  // Skip on server-side rendering to avoid hydration issues
  if (process.server) return;

  const { isAuthenticated, restoreSession } = useAuth();

  // Try to restore session first
  restoreSession();

  // If user is authenticated, redirect away from guest-only pages
  if (isAuthenticated.value) {
    // Check if there's a redirect parameter for where they were trying to go
    const redirectTo = (to.query.redirect as string) || '/dashboard';

    return navigateTo(redirectTo);
  }
});