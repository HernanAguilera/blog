import { User } from '../../domain/entities/user.entity';
import type { UserRepositoryInterface } from '../../domain/repositories/user-repository.interface';
import type { LoginCredentials, AuthResponse, LoginResult } from '../../domain/types/auth.types';

export class LoginUserUseCase {
    constructor(private readonly userRepository: UserRepositoryInterface) {}

    async execute(credentials: LoginCredentials): Promise<LoginResult> {
        try {
            // Validate input
            this.validateCredentials(credentials);

            // Attempt login
            const response: AuthResponse = await this.userRepository.login(credentials);

            if (!response.success) {
                return {
                    success: false,
                    message: response.message,
                    errors: response.errors
                };
            }

            if (!response.data) {
                return {
                    success: false,
                    message: 'Invalid response from server'
                };
            }

            // Create user entity from response
            const user = User.fromApiResponse(response.data.user);

            // Validate user can login
            if (!user.getIsActive()) {
                return {
                    success: false,
                    message: 'User account is deactivated'
                };
            }

            return {
                success: true,
                user,
                token: response.data.token,
                expiresAt: new Date(response.data.expires_at),
                message: response.message
            };

        } catch (error) {
            return {
                success: false,
                message: this.getErrorMessage(error)
            };
        }
    }

    private validateCredentials(credentials: LoginCredentials): void {
        const email = typeof credentials.email === 'string' ? credentials.email : '';
        const password = typeof credentials.password === 'string' ? credentials.password : '';

        if (!email || email.length === 0) {
            throw new Error('Email is required');
        }

        if (!password || password.length === 0) {
            throw new Error('Password is required');
        }

        // Basic email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            throw new Error('Invalid email format');
        }

        if (password.length < 6) {
            throw new Error('Password must be at least 6 characters');
        }
    }

    private getErrorMessage(error: unknown): string {
        if (error instanceof Error) {
            return error.message;
        }

        if (typeof error === 'string') {
            return error;
        }

        return 'An unexpected error occurred during login';
    }
}