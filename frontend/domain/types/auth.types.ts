export type LoginCredentials = {
    email: string;
    password: string;
    turnstileToken?: string;
};

export type RegisterData = {
    name: string;
    email: string;
    password: string;
    turnstileToken?: string;
};

export type AuthResponse = {
    success: boolean;
    message: string;
    data?: {
        token: string;
        expires_at: string;
        user: {
            id: string;
            name: string;
            email: string;
            role: string;
            is_active: boolean;
            email_verified: boolean;
        };
    };
    errors?: Record<string, string[]>;
};

export type UserData = {
    id: string;
    name: string;
    email: string;
    role: string;
    isActive: boolean;
    emailVerified: boolean;
    createdAt?: string;
    updatedAt?: string;
};

export type LoginResult = {
    success: boolean;
    user?: import('../entities/user.entity').User;
    token?: string;
    expiresAt?: Date;
    message: string;
    errors?: Record<string, string[]>;
};

export type RegisterResult = {
    success: boolean;
    user?: import('../entities/user.entity').User;
    token?: string;
    expiresAt?: Date;
    message: string;
    errors?: Record<string, string[]>;
};

export type LogoutResult = {
    success: boolean;
    message: string;
};