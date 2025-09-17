import type { User } from '../entities/user.entity';
import type { LoginCredentials, RegisterData, AuthResponse } from '../types/auth.types';

export interface UserRepositoryInterface {
    /**
     * Authenticate user with email and password
     */
    login(credentials: LoginCredentials): Promise<AuthResponse>;

    /**
     * Register a new user
     */
    register(data: RegisterData): Promise<AuthResponse>;

    /**
     * Logout current user (invalidate token)
     */
    logout(token: string): Promise<{ success: boolean; message: string }>;

    /**
     * Get current authenticated user data
     */
    getCurrentUser(token: string): Promise<User>;

    /**
     * Refresh authentication token
     */
    refreshToken(token: string): Promise<{ token: string; expires_at: string }>;

    /**
     * Verify if token is still valid
     */
    verifyToken(token: string): Promise<boolean>;

    /**
     * Get user by ID (admin functionality)
     */
    getUserById(id: string, token: string): Promise<User>;

    /**
     * Update user profile
     */
    updateProfile(
        id: string,
        data: Partial<{ name: string; email: string }>,
        token: string
    ): Promise<User>;

    /**
     * Change user password
     */
    changePassword(
        currentPassword: string,
        newPassword: string,
        token: string
    ): Promise<{ success: boolean; message: string }>;

    /**
     * Request password reset
     */
    requestPasswordReset(email: string): Promise<{ success: boolean; message: string }>;

    /**
     * Reset password with token
     */
    resetPassword(
        token: string,
        password: string
    ): Promise<{ success: boolean; message: string }>;

    /**
     * Verify email address
     */
    verifyEmail(token: string): Promise<{ success: boolean; message: string }>;

    /**
     * Request email verification
     */
    requestEmailVerification(token: string): Promise<{ success: boolean; message: string }>;
}