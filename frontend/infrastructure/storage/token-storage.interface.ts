import type { TokenData } from '../types/storage.types';

export interface TokenStorageInterface {
    getToken(): string | null;
    setToken(data: TokenData): void;
    removeToken(): void;
    isTokenExpired(): boolean;
    getTokenData(): TokenData | null;
    getExpirationTime(): Date | null;
}