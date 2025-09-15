import { User } from '../../domain/entities/user.entity';
import type { UserRepositoryInterface } from '../../domain/repositories/user-repository.interface';
import type { HttpClientInterface } from '../services/http-client.interface';
import type { LoginCredentials, RegisterData, AuthResponse } from '../../domain/types/auth.types';

export class HttpUserRepository implements UserRepositoryInterface {
    constructor(
        private readonly httpClient: HttpClientInterface,
        private readonly baseUrl: string = '/auth'
    ) {}

    async login(credentials: LoginCredentials): Promise<AuthResponse> {
        try {
            const response = await this.httpClient.post<AuthResponse>(
                `${this.baseUrl}/login`,
                {
                    email: credentials.email.trim().toLowerCase(),
                    password: credentials.password,
                    'cf-turnstile-response': credentials.turnstileToken
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            return this.handleError(error, 'Login failed');
        }
    }

    async register(data: RegisterData): Promise<AuthResponse> {
        try {
            const response = await this.httpClient.post<AuthResponse>(
                `${this.baseUrl}/register`,
                {
                    name: data.name.trim(),
                    email: data.email.trim().toLowerCase(),
                    password: data.password,
                    'cf-turnstile-response': data.turnstileToken
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            return this.handleError(error, 'Registration failed');
        }
    }

    async logout(token: string): Promise<{ success: boolean; message: string }> {
        try {
            const response = await this.httpClient.post<{ success: boolean; message: string }>(
                `${this.baseUrl}/logout`,
                {},
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            // For logout, we return success even if server fails
            // to ensure local cleanup can happen
            console.warn('Server logout failed:', error);
            return {
                success: true,
                message: 'Logged out locally'
            };
        }
    }

    async getCurrentUser(token: string): Promise<User> {
        try {
            const response = await this.httpClient.get<{ data: any }>(
                `${this.baseUrl}/me`,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            );

            return User.fromApiResponse(response.data);
        } catch (error) {
            throw new Error(`Failed to get current user: ${this.getErrorMessage(error)}`);
        }
    }

    async refreshToken(token: string): Promise<{ token: string; expires_at: string }> {
        try {
            const response = await this.httpClient.post<{ data: { token: string; expires_at: string } }>(
                `${this.baseUrl}/refresh`,
                {},
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response.data;
        } catch (error) {
            throw new Error(`Failed to refresh token: ${this.getErrorMessage(error)}`);
        }
    }

    async verifyToken(token: string): Promise<boolean> {
        try {
            await this.httpClient.post<any>(
                `${this.baseUrl}/verify`,
                {},
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return true;
        } catch (error) {
            return false;
        }
    }

    async getUserById(id: string, token: string): Promise<User> {
        try {
            const response = await this.httpClient.get<{ data: any }>(
                `/admin/users/${id}`,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            );

            return User.fromApiResponse(response.data);
        } catch (error) {
            throw new Error(`Failed to get user: ${this.getErrorMessage(error)}`);
        }
    }

    async updateProfile(
        id: string,
        data: Partial<{ name: string; email: string }>,
        token: string
    ): Promise<User> {
        try {
            const updateData: any = {};
            if (data.name) updateData.name = data.name.trim();
            if (data.email) updateData.email = data.email.trim().toLowerCase();

            const response = await this.httpClient.put<{ data: any }>(
                `${this.baseUrl}/profile`,
                updateData,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return User.fromApiResponse(response.data);
        } catch (error) {
            throw new Error(`Failed to update profile: ${this.getErrorMessage(error)}`);
        }
    }

    async changePassword(
        currentPassword: string,
        newPassword: string,
        token: string
    ): Promise<{ success: boolean; message: string }> {
        try {
            const response = await this.httpClient.put<{ success: boolean; message: string }>(
                `${this.baseUrl}/password`,
                {
                    current_password: currentPassword,
                    new_password: newPassword
                },
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to change password: ${this.getErrorMessage(error)}`);
        }
    }

    async requestPasswordReset(email: string): Promise<{ success: boolean; message: string }> {
        try {
            const response = await this.httpClient.post<{ success: boolean; message: string }>(
                `${this.baseUrl}/password/reset`,
                {
                    email: email.trim().toLowerCase()
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to request password reset: ${this.getErrorMessage(error)}`);
        }
    }

    async resetPassword(
        token: string,
        password: string
    ): Promise<{ success: boolean; message: string }> {
        try {
            const response = await this.httpClient.post<{ success: boolean; message: string }>(
                `${this.baseUrl}/password/reset/confirm`,
                {
                    token,
                    password
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to reset password: ${this.getErrorMessage(error)}`);
        }
    }

    async verifyEmail(token: string): Promise<{ success: boolean; message: string }> {
        try {
            const response = await this.httpClient.post<{ success: boolean; message: string }>(
                `${this.baseUrl}/email/verify`,
                {
                    token
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to verify email: ${this.getErrorMessage(error)}`);
        }
    }

    async requestEmailVerification(token: string): Promise<{ success: boolean; message: string }> {
        try {
            const response = await this.httpClient.post<{ success: boolean; message: string }>(
                `${this.baseUrl}/email/verification-notification`,
                {},
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            return response;
        } catch (error) {
            throw new Error(`Failed to request email verification: ${this.getErrorMessage(error)}`);
        }
    }

    private handleError(error: any, defaultMessage: string): AuthResponse {
        if (error?.response?.data) {
            return error.response.data;
        }

        if (error?.data) {
            return error.data;
        }

        return {
            success: false,
            message: defaultMessage,
            errors: {
                general: [this.getErrorMessage(error)]
            }
        };
    }

    private getErrorMessage(error: any): string {
        if (error?.message) {
            return error.message;
        }

        if (error?.response?.data?.message) {
            return error.response.data.message;
        }

        if (typeof error === 'string') {
            return error;
        }

        return 'An unexpected error occurred';
    }
}