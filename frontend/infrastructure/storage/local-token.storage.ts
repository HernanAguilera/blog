import type { TokenStorageInterface } from './token-storage.interface';
import type { TokenData } from '../types/storage.types';

export class LocalTokenStorage implements TokenStorageInterface {
    private readonly TOKEN_KEY = 'auth_token';
    private readonly TOKEN_DATA_KEY = 'auth_token_data';

    getToken(): string | null {
        if (typeof window === 'undefined') {
            return null; // SSR safety
        }

        try {
            const tokenData = this.getTokenData();

            if (!tokenData) {
                return null;
            }

            // Check if token is expired
            if (this.isTokenExpired()) {
                this.removeToken();
                return null;
            }

            return tokenData.token;
        } catch (error) {
            console.error('Error getting token:', error);
            this.removeToken(); // Clean up corrupted data
            return null;
        }
    }

    setToken(data: TokenData): void {
        if (typeof window === 'undefined') {
            return; // SSR safety
        }

        try {
            // Validate token data
            this.validateTokenData(data);

            // Store both token and full data
            localStorage.setItem(this.TOKEN_KEY, data.token);
            localStorage.setItem(this.TOKEN_DATA_KEY, JSON.stringify(data));
        } catch (error) {
            console.error('Error setting token:', error);
            throw new Error('Failed to store authentication token');
        }
    }

    removeToken(): void {
        if (typeof window === 'undefined') {
            return; // SSR safety
        }

        try {
            localStorage.removeItem(this.TOKEN_KEY);
            localStorage.removeItem(this.TOKEN_DATA_KEY);
        } catch (error) {
            console.error('Error removing token:', error);
        }
    }

    isTokenExpired(): boolean {
        const expirationTime = this.getExpirationTime();

        if (!expirationTime) {
            return true;
        }

        // Add 1 minute buffer to account for clock skew
        const bufferTime = 60 * 1000; // 1 minute in milliseconds
        return Date.now() > (expirationTime.getTime() - bufferTime);
    }

    getTokenData(): TokenData | null {
        if (typeof window === 'undefined') {
            return null; // SSR safety
        }

        try {
            const dataString = localStorage.getItem(this.TOKEN_DATA_KEY);

            if (!dataString) {
                return null;
            }

            const data = JSON.parse(dataString) as TokenData;

            // Validate the parsed data
            this.validateTokenData(data);

            return data;
        } catch (error) {
            console.error('Error getting token data:', error);
            this.removeToken(); // Clean up corrupted data
            return null;
        }
    }

    getExpirationTime(): Date | null {
        const tokenData = this.getTokenData();

        if (!tokenData?.expiresAt) {
            return null;
        }

        try {
            return new Date(tokenData.expiresAt);
        } catch (error) {
            console.error('Error parsing expiration time:', error);
            return null;
        }
    }

    // Utility methods
    public getRemainingTime(): number {
        const expirationTime = this.getExpirationTime();

        if (!expirationTime) {
            return 0;
        }

        const remainingMs = expirationTime.getTime() - Date.now();
        return Math.max(0, remainingMs);
    }

    public getRemainingTimeFormatted(): string {
        const remainingMs = this.getRemainingTime();

        if (remainingMs === 0) {
            return 'Expired';
        }

        const minutes = Math.floor(remainingMs / (1000 * 60));
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        if (days > 0) {
            return `${days} day${days > 1 ? 's' : ''}`;
        }

        if (hours > 0) {
            return `${hours} hour${hours > 1 ? 's' : ''}`;
        }

        return `${minutes} minute${minutes > 1 ? 's' : ''}`;
    }

    public willExpireSoon(minutesBefore: number = 10): boolean {
        const remainingMs = this.getRemainingTime();
        const thresholdMs = minutesBefore * 60 * 1000;

        return remainingMs > 0 && remainingMs <= thresholdMs;
    }

    // Security methods
    public clearAllAuthData(): void {
        if (typeof window === 'undefined') {
            return;
        }

        try {
            // Remove all auth-related items
            const authKeys = [
                this.TOKEN_KEY,
                this.TOKEN_DATA_KEY,
                'user_data',
                'auth_remember_me'
            ];

            authKeys.forEach(key => {
                localStorage.removeItem(key);
            });

            // Also clear session storage
            authKeys.forEach(key => {
                sessionStorage.removeItem(key);
            });
        } catch (error) {
            console.error('Error clearing auth data:', error);
        }
    }

    private validateTokenData(data: TokenData): void {
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid token data format');
        }

        if (!data.token || typeof data.token !== 'string') {
            throw new Error('Invalid token');
        }

        if (!data.expiresAt || typeof data.expiresAt !== 'string') {
            throw new Error('Invalid expiration date');
        }

        // Basic JWT format validation
        const tokenParts = data.token.split('.');
        if (tokenParts.length !== 3) {
            throw new Error('Invalid JWT token format');
        }

        // Validate expiration date format
        const expirationDate = new Date(data.expiresAt);
        if (isNaN(expirationDate.getTime())) {
            throw new Error('Invalid expiration date format');
        }
    }
}