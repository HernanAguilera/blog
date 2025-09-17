export interface HttpClientInterface {
    post<T>(url: string, data: any, options?: any): Promise<T>;
    get<T>(url: string, options?: any): Promise<T>;
    put<T>(url: string, data: any, options?: any): Promise<T>;
    patch<T>(url: string, data: any, options?: any): Promise<T>;
    delete<T>(url: string, options?: any): Promise<T>;
}