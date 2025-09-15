import { User } from '../../domain/entities/user.entity';
import type { UserRepositoryInterface } from '../../domain/repositories/user-repository.interface';
import type { RegisterData, AuthResponse, RegisterResult } from '../../domain/types/auth.types';

export class RegisterUserUseCase {
    constructor(private readonly userRepository: UserRepositoryInterface) {}

    async execute(data: RegisterData): Promise<RegisterResult> {
        try {
            // Validate input
            this.validateRegistrationData(data);

            // Attempt registration
            const response: AuthResponse = await this.userRepository.register(data);

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

    private validateRegistrationData(data: RegisterData): void {
        // Name validation
        if (!data.name || data.name.trim() === '') {
            throw new Error('Name is required');
        }

        if (data.name.trim().length < 2) {
            throw new Error('Name must be at least 2 characters long');
        }

        if (data.name.trim().length > 100) {
            throw new Error('Name cannot exceed 100 characters');
        }

        // Email validation
        if (!data.email || data.email.trim() === '') {
            throw new Error('Email is required');
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email.trim())) {
            throw new Error('Invalid email format');
        }

        if (data.email.length > 254) {
            throw new Error('Email is too long');
        }

        // Password validation
        if (!data.password || data.password.trim() === '') {
            throw new Error('Password is required');
        }

        if (data.password.length < 8) {
            throw new Error('Password must be at least 8 characters long');
        }

        if (data.password.length > 255) {
            throw new Error('Password is too long');
        }

        // Password strength validation
        this.validatePasswordStrength(data.password);
    }

    private validatePasswordStrength(password: string): void {
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        const strengthChecks = [hasUpperCase, hasLowerCase, hasNumbers, hasSpecialChar];
        const passedChecks = strengthChecks.filter(Boolean).length;

        if (passedChecks < 3) {
            throw new Error(
                'Password must contain at least 3 of the following: uppercase letters, lowercase letters, numbers, special characters'
            );
        }

        // Common password patterns
        const commonPatterns = [
            /^123+/,
            /^abc+/i,
            /^password/i,
            /^qwerty/i,
            /(.)\1{2,}/ // repeated characters
        ];

        for (const pattern of commonPatterns) {
            if (pattern.test(password)) {
                throw new Error('Password is too common or contains repetitive patterns');
            }
        }
    }

    private getErrorMessage(error: unknown): string {
        if (error instanceof Error) {
            return error.message;
        }

        if (typeof error === 'string') {
            return error;
        }

        return 'An unexpected error occurred during registration';
    }
}