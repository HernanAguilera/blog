/**
 * Guest middleware - protects routes that should only be accessible to unauthenticated users
 * Redirects authenticated users to dashboard or intended destination
 */
import { useAuthStore } from '~/interface/stores/auth.store';

export default defineNuxtRouteMiddleware(async (to) => {
  // Skip on server-side rendering to avoid hydration issues
  if (import.meta.server) return;

  const authStore = useAuthStore();

  // Try to restore session first (async)
  await authStore.restoreSession();

  // If user is authenticated, redirect away from guest-only pages
  if (authStore.isLoggedIn) {
    // Check if there's a redirect parameter for where they were trying to go
    const redirectTo = (to.query.redirect as string) || '/profile';

    return navigateTo(redirectTo);
  }
});