import type { TokenStorageInterface } from '../storage/token-storage.interface';
import type { HttpClientInterface } from './http-client.interface';
import type { HttpClientConfig, HttpError } from '../types/http.types';

// Import loading store - will be available globally in browser context
let loadingStore: any = null;

// Lazy load the store to avoid SSR issues
const getLoadingStore = () => {
    if (typeof window === 'undefined') return null;

    if (!loadingStore) {
        try {
            // Dynamic import to avoid SSR issues
            const { useLoadingStore } = require('~/interface/stores/loading.store');
            loadingStore = useLoadingStore();
        } catch (error) {
            // Store not available, continue without loading integration
            return null;
        }
    }
    return loadingStore;
};

export class HttpClientService implements HttpClientInterface {
    private readonly config: Required<HttpClientConfig>;

    constructor(
        private readonly tokenStorage: TokenStorageInterface,
        config: HttpClientConfig = {}
    ) {
        this.config = {
            timeout: config.timeout ?? 30000,
            retries: config.retries ?? 3,
            retryDelay: config.retryDelay ?? 1000,
            baseURL: config.baseURL ?? ''
        };
    }

    async get<T>(url: string, options: any = {}): Promise<T> {
        return this.request<T>('GET', url, undefined, options);
    }

    async post<T>(url: string, data: any = {}, options: any = {}): Promise<T> {
        return this.request<T>('POST', url, data, options);
    }

    async put<T>(url: string, data: any = {}, options: any = {}): Promise<T> {
        return this.request<T>('PUT', url, data, options);
    }

    async patch<T>(url: string, data: any = {}, options: any = {}): Promise<T> {
        return this.request<T>('PATCH', url, data, options);
    }

    async delete<T>(url: string, options: any = {}): Promise<T> {
        return this.request<T>('DELETE', url, undefined, options);
    }

    private async request<T>(
        method: string,
        url: string,
        data?: any,
        options: any = {}
    ): Promise<T> {
        const fullUrl = this.buildUrl(url);
        const headers = this.buildHeaders(options.headers);

        // Auto-loading integration
        const loadingStore = getLoadingStore();
        const shouldShowLoading = options.loading !== false; // Allow opt-out with loading: false

        if (loadingStore && shouldShowLoading) {
            loadingStore.show();
        }

        let lastError: any;
        let attempt = 0;

        try {
            while (attempt <= this.config.retries) {
                try {
                    const response = await this.performRequest<T>(method, fullUrl, data, {
                        ...options,
                        headers
                    });

                    return response;
                } catch (error) {
                    lastError = error;
                    attempt++;

                    // Don't retry on authentication errors or client errors
                    if (this.shouldNotRetry(error)) {
                        break;
                    }

                    // Don't retry on last attempt
                    if (attempt <= this.config.retries) {
                        await this.delay(this.config.retryDelay * attempt);
                    }
                }
            }
        } finally {
            // Always clean up loading state
            if (loadingStore && shouldShowLoading) {
                loadingStore.hide();
            }
        }

        throw lastError;
    }

    private async performRequest<T>(
        method: string,
        url: string,
        data?: any,
        options: any = {}
    ): Promise<T> {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.config.timeout);

        try {
            const fetchOptions: RequestInit = {
                method,
                headers: options.headers,
                signal: controller.signal,
                ...this.buildRequestOptions(data, options)
            };

            const response = await fetch(url, fetchOptions);
            clearTimeout(timeoutId);

            await this.handleResponse(response);

            const responseData = await this.parseResponse<T>(response);
            return responseData;
        } catch (error) {
            clearTimeout(timeoutId);

            if (error instanceof Error && error.name === 'AbortError') {
                throw new Error('Request timeout');
            }

            throw error;
        }
    }

    private buildUrl(url: string): string {
        if (url.startsWith('http://') || url.startsWith('https://')) {
            return url;
        }

        const baseURL = this.config.baseURL.replace(/\/$/, '');
        const cleanUrl = url.replace(/^\//, '');

        return `${baseURL}/${cleanUrl}`;
    }

    private buildHeaders(additionalHeaders: Record<string, string> = {}): Record<string, string> {
        const headers: Record<string, string> = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...additionalHeaders
        };

        // Add authentication header if token exists
        const token = this.tokenStorage.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        // Add locale header for i18n support
        if (typeof window !== 'undefined') {
            try {
                const { locale } = useI18n();
                headers['Accept-Language'] = locale.value || 'es';
            } catch {
                // i18n not available, use default locale
                headers['Accept-Language'] = 'es';
            }
        } else {
            // SSR context - use default locale
            headers['Accept-Language'] = 'es';
        }

        return headers;
    }

    private buildRequestOptions(data?: any, options: any = {}): Partial<RequestInit> {
        const requestOptions: Partial<RequestInit> = {
            credentials: 'include'
        };

        if (data !== undefined) {
            if (data instanceof FormData) {
                requestOptions.body = data;
                // Don't set Content-Type for FormData, let browser set it with boundary
            } else if (typeof data === 'object') {
                requestOptions.body = JSON.stringify(data);
            } else {
                requestOptions.body = String(data);
            }
        }

        return requestOptions;
    }

    private async handleResponse(response: Response): Promise<void> {
        // Handle token expiration
        if (response.status === 401) {
            this.tokenStorage.removeToken();

            // If we're not already on a login page, redirect to login
            if (typeof window !== 'undefined' && !window.location.pathname.includes('/auth/login')) {
                window.location.href = '/auth/login';
            }
        }

        // Handle rate limiting
        if (response.status === 429) {
            const retryAfter = response.headers.get('Retry-After');
            const errorMessage = retryAfter
                ? `Rate limited. Retry after ${retryAfter} seconds.`
                : 'Rate limited. Please try again later.';

            throw this.createHttpError(errorMessage, response.status, await this.parseErrorResponse(response));
        }

        // Handle server errors
        if (response.status >= 500) {
            throw this.createHttpError(
                'Server error. Please try again later.',
                response.status,
                await this.parseErrorResponse(response)
            );
        }

        // Handle client errors
        if (response.status >= 400) {
            const errorData = await this.parseErrorResponse(response);
            const errorMessage = errorData?.message || `Request failed with status ${response.status}`;

            throw this.createHttpError(errorMessage, response.status, errorData);
        }
    }

    private async parseResponse<T>(response: Response): Promise<T> {
        const contentType = response.headers.get('Content-Type') || '';

        if (contentType.includes('application/json')) {
            return response.json();
        }

        if (contentType.includes('text/')) {
            return response.text() as any;
        }

        return response.blob() as any;
    }

    private async parseErrorResponse(response: Response): Promise<any> {
        try {
            const contentType = response.headers.get('Content-Type') || '';

            if (contentType.includes('application/json')) {
                return await response.json();
            }

            return { message: await response.text() };
        } catch {
            return { message: response.statusText };
        }
    }

    private createHttpError(message: string, status?: number, responseData?: any): HttpError {
        const error: HttpError = {
            message,
            status,
            response: responseData ? {
                data: responseData,
                status: status || 0,
                statusText: message
            } : undefined
        };

        return error;
    }

    private shouldNotRetry(error: any): boolean {
        const status = error.status || error.response?.status;

        // Don't retry on authentication errors, client errors, or specific server errors
        return status === 401 || status === 403 || status === 404 || status === 422;
    }

    private delay(ms: number): Promise<void> {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Utility methods for configuration
    public setTimeout(timeout: number): void {
        this.config.timeout = timeout;
    }

    public setRetries(retries: number): void {
        this.config.retries = retries;
    }

    public setRetryDelay(delay: number): void {
        this.config.retryDelay = delay;
    }
}