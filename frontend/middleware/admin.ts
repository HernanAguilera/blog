/**
 * Admin middleware - protects routes that require admin or super admin permissions
 * Redirects non-admin users to dashboard with error message
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
    // Force overlay to show immediately by adding it to DOM if not exists
    if (!document.querySelector('#admin-loading-overlay')) {
      const overlay = document.createElement('div');
      overlay.id = 'admin-loading-overlay';
      overlay.style.cssText = `
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
      `;
      overlay.innerHTML = `
        <div style="background: white; border-radius: 8px; padding: 24px; text-align: center; max-width: 400px; margin: 16px;">
          <div style="display: flex; justify-content: center; margin-bottom: 16px;">
            <div style="width: 48px; height: 48px; border: 4px solid #e5e7eb; border-top: 4px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
          </div>
          <div style="color: #374151; font-size: 16px;">Cargando...</div>
        </div>
      `;

      // Add CSS animation
      const style = document.createElement('style');
      style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
      document.head.appendChild(style);

      document.body.appendChild(overlay);
    }

    // Give the overlay time to render
    await new Promise(resolve => setTimeout(resolve, 500));

    // Try to restore session first (await to ensure completion)
    await authStore.restoreSession();

    // First check if user is authenticated
    if (!authStore.isLoggedIn) {
      // Keep loading visible for a minimum time so user sees it
      await new Promise(resolve => setTimeout(resolve, 1000));

      // Remove manual overlay
      const overlay = document.querySelector('#admin-loading-overlay');
      if (overlay) overlay.remove();

      loadingStore.hide();
      return navigateTo('/auth/login');
    }

    // Check if user has admin role or higher
    const currentUser = authStore.currentUser;

    if (!currentUser || (!currentUser.hasRole(ROLE.ADMIN) && !currentUser.hasRole(ROLE.SUPER_ADMIN))) {
      // Keep loading visible for minimum time so user sees it
      await new Promise(resolve => setTimeout(resolve, 1000));

      // Remove manual overlay
      const overlay = document.querySelector('#admin-loading-overlay');
      if (overlay) overlay.remove();

      loadingStore.hide();
      // Show 404 instead of revealing route exists
      throw createError({
        statusCode: 404,
        statusMessage: 'Página no encontrada'
      });
    }

    // All checks passed, stop loading
    const overlay = document.querySelector('#admin-loading-overlay');
    if (overlay) overlay.remove();
    loadingStore.hide();
  } catch (error) {
    // Clean up loading on error
    const overlay = document.querySelector('#admin-loading-overlay');
    if (overlay) overlay.remove();
    loadingStore.hide();
    throw error;
  }
});