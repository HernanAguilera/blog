import { computed, type ComputedRef } from 'vue';
import { useAuthStore } from '../stores/auth.store';
import type { User } from '../../domain/entities/user.entity';
import type { LoginPayload, RegisterPayload } from '../types/auth-store.types';
import type { PermissionType, RoleType } from '../../domain/types/permissions.types';

export interface UseAuthReturn {
    // State
    user: ComputedRef<User | null>;
    isAuthenticated: ComputedRef<boolean>;
    isLoading: ComputedRef<boolean>;
    error: ComputedRef<string | null>;
    errors: ComputedRef<Record<string, string[]> | null>;

    // User info
    userRole: ComputedRef<string | null>;
    userName: ComputedRef<string | null>;
    userEmail: ComputedRef<string | null>;

    // Session info
    isLoggedIn: ComputedRef<boolean>;
    hasValidToken: ComputedRef<boolean>;
    isSessionExpired: ComputedRef<boolean>;
    timeToExpiry: ComputedRef<number>;

    // Loading states
    isLoggingIn: ComputedRef<boolean>;
    isRegistering: ComputedRef<boolean>;
    isLoggingOut: ComputedRef<boolean>;
    isAnyLoading: ComputedRef<boolean>;

    // Actions
    login: (payload: LoginPayload) => Promise<{ success: boolean; message: string; errors?: Record<string, string[]> }>;
    register: (payload: RegisterPayload) => Promise<{ success: boolean; message: string; errors?: Record<string, string[]> }>;
    logout: () => Promise<{ success: boolean }>;
    restoreSession: () => Promise<boolean>;

    // Authorization
    can: (permission: PermissionType) => boolean;
    is: (role: RoleType) => boolean;
    canAny: (permissions: PermissionType[]) => boolean;
    canAll: (permissions: PermissionType[]) => boolean;

    // Utilities
    clearErrors: () => void;
    updateLastActivity: () => void;
    checkSessionExpiry: () => void;
    getFieldError: (field: string) => string | null;
}

/**
 * Composable for authentication management
 *
 * Provides reactive access to authentication state and actions
 * Wraps the Pinia store with a clean composable interface
 */
export function useAuth(): UseAuthReturn {
    const authStore = useAuthStore();

    return {
        // Reactive state
        user: computed(() => authStore.currentUser),
        isAuthenticated: computed(() => authStore.isAuthenticated),
        isLoading: computed(() => authStore.isLoading),
        error: computed(() => authStore.error),
        errors: computed(() => authStore.errors),

        // User info
        userRole: computed(() => authStore.userRole),
        userName: computed(() => authStore.userName),
        userEmail: computed(() => authStore.userEmail),

        // Session info
        isLoggedIn: computed(() => authStore.isLoggedIn),
        hasValidToken: computed(() => authStore.hasValidToken),
        isSessionExpired: computed(() => authStore.isSessionExpired),
        timeToExpiry: computed(() => authStore.timeToExpiry),

        // Loading states
        isLoggingIn: computed(() => authStore.isLoggingIn),
        isRegistering: computed(() => authStore.isRegistering),
        isLoggingOut: computed(() => authStore.isLoggingOut),
        isAnyLoading: computed(() => authStore.isAnyLoading),

        // Actions
        login: authStore.login,
        register: authStore.register,
        logout: authStore.logout,
        restoreSession: authStore.restoreSession,

        // Authorization methods
        can: authStore.can,
        is: authStore.is,
        canAny: authStore.canAny,
        canAll: authStore.canAll,

        // Utilities
        clearErrors: authStore.clearErrors,
        updateLastActivity: authStore.updateLastActivity,
        checkSessionExpiry: authStore.checkSessionExpiry,
        getFieldError: authStore.getFieldError
    };
}

/**
 * Simplified composable for read-only auth state
 * Use this when you only need to check authentication status
 */
export function useAuthState() {
    const authStore = useAuthStore();

    return {
        user: computed(() => authStore.currentUser),
        isAuthenticated: computed(() => authStore.isAuthenticated),
        isLoggedIn: computed(() => authStore.isLoggedIn),
        userRole: computed(() => authStore.userRole),
        userName: computed(() => authStore.userName),
        isLoading: computed(() => authStore.isAnyLoading)
    };
}

/**
 * Composable specifically for authorization checks
 * Use this in components that need permission/role validation
 */
export function useAuthz() {
    const authStore = useAuthStore();

    return {
        can: authStore.can,
        is: authStore.is,
        canAny: authStore.canAny,
        canAll: authStore.canAll,
        user: computed(() => authStore.currentUser),
        isAuthenticated: computed(() => authStore.isAuthenticated)
    };
}