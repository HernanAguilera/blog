import { defineStore } from 'pinia';
import { User } from '../../domain/entities/user.entity';
import { LoginUserUseCase } from '../../application/use-cases/login-user.use-case';
import { RegisterUserUseCase } from '../../application/use-cases/register-user.use-case';
import { LogoutUserUseCase } from '../../application/use-cases/logout-user.use-case';
import { AuthorizationService } from '../../application/services/authorization.service';
import type { TokenStorageInterface } from '../../infrastructure/storage/token-storage.interface';
import type { AuthState, LoginPayload, RegisterPayload } from '../types/auth-store.types';
import type { PermissionType, RoleType } from '../../domain/types/permissions.types';

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => ({
        // Authentication state
        user: null,
        token: null,
        isAuthenticated: false,

        // Loading states
        isLoading: false,
        isLoggingIn: false,
        isRegistering: false,
        isLoggingOut: false,

        // Error states
        error: null,
        errors: null,

        // Session info
        expiresAt: null,
        lastActivity: null
    }),

    getters: {
        // User info getters
        currentUser: (state): User | null => state.user as User | null,

        userRole: (state): string | null => state.user?.getRole().value() || null,

        userName: (state): string | null => state.user?.getName() || null,

        userEmail: (state): string | null => state.user?.getEmail().value() || null,

        // Session getters
        isLoggedIn: (state): boolean => state.isAuthenticated && !!state.user && !!state.token,

        hasValidToken: (state): boolean => {
            return !!state.token && !!state.expiresAt && state.expiresAt > new Date();
        },

        isSessionExpired: (state): boolean => {
            return !!state.expiresAt && state.expiresAt <= new Date();
        },

        timeToExpiry: (state): number => {
            if (!state.expiresAt) return 0;
            return Math.max(0, state.expiresAt.getTime() - Date.now());
        },

        // Loading state getters
        isAnyLoading: (state): boolean => {
            return state.isLoading || state.isLoggingIn || state.isRegistering || state.isLoggingOut;
        },

        // Error getters
        hasErrors: (state): boolean => !!state.error || !!state.errors,

        getFieldError: (state) => (field: string): string | null => {
            return state.errors?.[field]?.[0] || null;
        }
    },

    actions: {
        // Dependency injection (will be set by container)
        _loginUseCase: null as LoginUserUseCase | null,
        _registerUseCase: null as RegisterUserUseCase | null,
        _logoutUseCase: null as LogoutUserUseCase | null,
        _authService: null as AuthorizationService | null,
        _tokenStorage: null as TokenStorageInterface | null,

        // Initialize dependencies
        initializeDependencies(
            loginUseCase: LoginUserUseCase,
            registerUseCase: RegisterUserUseCase,
            logoutUseCase: LogoutUserUseCase,
            authService: AuthorizationService,
            tokenStorage: TokenStorageInterface
        ) {
            this._loginUseCase = loginUseCase;
            this._registerUseCase = registerUseCase;
            this._logoutUseCase = logoutUseCase;
            this._authService = authService;
            this._tokenStorage = tokenStorage;
        },

        // Authentication actions
        async login(payload: LoginPayload) {
            if (!this._loginUseCase) throw new Error('LoginUseCase not initialized');

            this.clearErrors();
            this.isLoggingIn = true;

            try {
                const result = await this._loginUseCase.execute({
                    email: payload.email,
                    password: payload.password,
                    turnstileToken: payload.turnstileToken
                });

                if (result.success && result.user && result.token && result.expiresAt) {
                    this.setAuthenticatedUser(result.user, result.token, result.expiresAt);

                    // Store token for persistence
                    this._tokenStorage?.setToken({
                        token: result.token,
                        expiresAt: result.expiresAt.toISOString()
                    });

                    return { success: true, message: result.message };
                } else {
                    this.setError(result.message, result.errors);
                    return { success: false, message: result.message, errors: result.errors };
                }
            } catch (error) {
                const message = error instanceof Error ? error.message : 'Login failed';
                this.setError(message);
                return { success: false, message };
            } finally {
                this.isLoggingIn = false;
            }
        },

        async register(payload: RegisterPayload) {
            if (!this._registerUseCase) throw new Error('RegisterUseCase not initialized');

            this.clearErrors();
            this.isRegistering = true;

            try {
                const result = await this._registerUseCase.execute({
                    name: payload.name,
                    email: payload.email,
                    password: payload.password,
                    turnstileToken: payload.turnstileToken
                });

                if (result.success && result.user && result.token && result.expiresAt) {
                    this.setAuthenticatedUser(result.user, result.token, result.expiresAt);

                    // Store token for persistence
                    this._tokenStorage?.setToken({
                        token: result.token,
                        expiresAt: result.expiresAt.toISOString()
                    });

                    return { success: true, message: result.message };
                } else {
                    this.setError(result.message, result.errors);
                    return { success: false, message: result.message, errors: result.errors };
                }
            } catch (error) {
                const message = error instanceof Error ? error.message : 'Registration failed';
                this.setError(message);
                return { success: false, message };
            } finally {
                this.isRegistering = false;
            }
        },

        async logout() {
            if (!this._logoutUseCase) throw new Error('LogoutUseCase not initialized');

            this.isLoggingOut = true;

            try {
                await this._logoutUseCase.execute(this.token || undefined);
                this.clearAuthenticatedUser();
                return { success: true };
            } catch (error) {
                // Even if server logout fails, clear local session
                this.clearAuthenticatedUser();
                return { success: true };
            } finally {
                this.isLoggingOut = false;
            }
        },

        // Session management
        async restoreSession() {
            if (!this._tokenStorage) return false;

            this.isLoading = true;

            try {
                const tokenData = this._tokenStorage.getTokenData();

                if (!tokenData || this._tokenStorage.isTokenExpired()) {
                    this.clearAuthenticatedUser();
                    return false;
                }

                // TODO: Validate token with server and get user data
                // For now, we'll need to implement getCurrentUser in the repository
                this.token = tokenData.token;
                this.expiresAt = new Date(tokenData.expiresAt);
                this.isAuthenticated = true;
                this.updateLastActivity();

                return true;
            } catch (error) {
                this.clearAuthenticatedUser();
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        // Authorization methods (delegated to AuthorizationService)
        can(permission: PermissionType): boolean {
            if (!this.user || !this._authService) return false;
            return this._authService.can(this.user as User, permission);
        },

        is(role: RoleType): boolean {
            if (!this.user || !this._authService) return false;
            return this._authService.is(this.user as User, role);
        },

        canAny(permissions: PermissionType[]): boolean {
            if (!this.user || !this._authService) return false;
            return this._authService.canAny(this.user as User, permissions);
        },

        canAll(permissions: PermissionType[]): boolean {
            if (!this.user || !this._authService) return false;
            return this._authService.canAll(this.user as User, permissions);
        },

        // State management helpers
        setAuthenticatedUser(user: User, token: string, expiresAt: Date) {
            this.user = user;
            this.token = token;
            this.expiresAt = expiresAt;
            this.isAuthenticated = true;
            this.updateLastActivity();
            this.clearErrors();
        },

        clearAuthenticatedUser() {
            this.user = null;
            this.token = null;
            this.expiresAt = null;
            this.isAuthenticated = false;
            this.lastActivity = null;
            this._tokenStorage?.removeToken();
            this.clearErrors();
        },

        setError(message: string, errors?: Record<string, string[]> | null) {
            this.error = message;
            this.errors = errors || null;
        },

        clearErrors() {
            this.error = null;
            this.errors = null;
        },

        updateLastActivity() {
            this.lastActivity = new Date();
        },

        // Session monitoring
        checkSessionExpiry() {
            if (this.isSessionExpired) {
                this.clearAuthenticatedUser();

                // Redirect to login if we're in browser
                if (typeof window !== 'undefined') {
                    window.location.href = '/auth/login';
                }
            }
        }
    }
});