/**
 * Guest middleware - protects routes that should only be accessible to unauthenticated users
 * Redirects authenticated users to dashboard or intended destination
 */
import { useAuthStore } from '~/interface/stores/auth.store';

export default defineNuxtRouteMiddleware(async (to) => {
  // Skip on server-side rendering to avoid hydration issues
  if (process.server) return;

  const authStore = useAuthStore();

  // Try to restore session first (async)
  await authStore.restoreSession();

  // If user is authenticated, redirect away from guest-only pages
  if (authStore.isAuthenticated) {
    // Check if there's a redirect parameter for where they were trying to go
    const redirectTo = (to.query.redirect as string) || '/dashboard';

    return navigateTo(redirectTo);
  }
});