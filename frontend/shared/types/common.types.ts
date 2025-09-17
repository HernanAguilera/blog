// Generic pagination types
export type PaginationData = {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from?: number;
    to?: number;
};

export type PaginatedResponse<T> = {
    data: T[];
    pagination: PaginationData;
};

// Generic filter base
export type BaseFilter = {
    search?: string;
    page?: number;
    perPage?: number;
    sortBy?: string;
    sortDirection?: 'asc' | 'desc';
};

// Generic date range filter
export type DateRangeFilter = {
    dateFrom?: string;
    dateTo?: string;
};

// Generic status filter
export type StatusFilter = {
    status?: string;
};

// Generic author filter
export type AuthorFilter = {
    authorId?: string;
};

// Common API response types
export type ApiResponse<T = any> = {
    success: boolean;
    message: string;
    data?: T;
    errors?: Record<string, string[]>;
};

export type ApiError = {
    success: false;
    message: string;
    errors?: Record<string, string[]>;
};

// Common form validation types
export type ValidationErrors = Record<string, string[]>;

export type FormState = {
    isSubmitting: boolean;
    hasErrors: boolean;
    errors: ValidationErrors;
};

// Generic sort options
export type SortOption = {
    value: string;
    label: string;
    direction?: 'asc' | 'desc';
};

// Generic loading states
export type LoadingState = 'idle' | 'loading' | 'success' | 'error';

export type AsyncState<T = any> = {
    data: T | null;
    loading: LoadingState;
    error: string | null;
};