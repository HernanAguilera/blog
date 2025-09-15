export type HttpClientConfig = {
    timeout?: number;
    retries?: number;
    retryDelay?: number;
    baseURL?: string;
};

export type HttpResponse<T = any> = {
    data: T;
    status: number;
    statusText: string;
    headers: Record<string, string>;
};

export type HttpError = {
    message: string;
    status?: number;
    response?: {
        data: any;
        status: number;
        statusText: string;
    };
};