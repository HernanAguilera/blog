export type FormValidationRule = {
    required?: boolean;
    minLength?: number;
    maxLength?: number;
    pattern?: RegExp;
    message: string;
};

export type FormField = {
    value: string;
    error: string | null;
    rules: FormValidationRule[];
    touched: boolean;
    dirty: boolean;
};

export type FormState = {
    isValid: boolean;
    isSubmitting: boolean;
    hasErrors: boolean;
    fields: Record<string, FormField>;
};

export type TurnstileWidget = {
    render: (element: string | Element, options: TurnstileOptions) => string;
    reset: (widgetId?: string) => void;
    remove: (widgetId?: string) => void;
    getResponse: (widgetId?: string) => string | undefined;
};

export type TurnstileOptions = {
    sitekey: string;
    callback?: (token: string) => void;
    'error-callback'?: () => void;
    'expired-callback'?: () => void;
    theme?: 'light' | 'dark' | 'auto';
    size?: 'normal' | 'compact';
    'response-field'?: boolean;
    'response-field-name'?: string;
};

declare global {
    interface Window {
        turnstile?: TurnstileWidget;
    }
}