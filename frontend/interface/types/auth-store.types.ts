import type { User } from '../../domain/entities/user.entity';

export type AuthState = {
    // Authentication state
    user: User | null;
    token: string | null;
    isAuthenticated: boolean;

    // Loading states
    isLoading: boolean;
    isLoggingIn: boolean;
    isRegistering: boolean;
    isLoggingOut: boolean;

    // Error states
    error: string | null;
    errors: Record<string, string[]> | null;

    // Session info
    expiresAt: Date | null;
    lastActivity: Date | null;
};

export type LoginPayload = {
    email: string;
    password: string;
    remember?: boolean;
    turnstileToken?: string;
};

export type RegisterPayload = {
    name: string;
    email: string;
    password: string;
    turnstileToken?: string;
};