import type { UserRepositoryInterface } from '../../domain/repositories/user-repository.interface';
import type { LogoutResult } from '../../domain/types/auth.types';

export class LogoutUserUseCase {
    constructor(private readonly userRepository: UserRepositoryInterface) {}

    async execute(token?: string): Promise<LogoutResult> {
        try {
            // If no token provided, consider it a local logout only
            if (!token || token.trim() === '') {
                return {
                    success: true,
                    message: 'Logged out locally'
                };
            }

            // Validate token format (basic check)
            this.validateToken(token);

            // Attempt server-side logout
            const response = await this.userRepository.logout(token);

            return {
                success: response.success,
                message: response.message
            };

        } catch (error) {
            // Even if server logout fails, we should still log out locally
            // This ensures the user can always clear their session
            return {
                success: true,
                message: 'Logged out locally (server logout failed)'
            };
        }
    }

    private validateToken(token: string): void {
        if (token.length < 10) {
            throw new Error('Invalid token format');
        }

        // Basic JWT format check (header.payload.signature)
        const parts = token.split('.');
        if (parts.length !== 3) {
            throw new Error('Invalid JWT token format');
        }

        // Check if parts are base64 encoded (basic validation)
        for (const part of parts) {
            if (!/^[A-Za-z0-9_-]+$/.test(part)) {
                throw new Error('Invalid token encoding');
            }
        }
    }
}