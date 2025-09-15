# TypeScript Conventions and Rules

## Interface vs Type Usage

### Rule: Separation of Concerns
- **Interfaces**: ONLY for defining abstractions and contracts
- **Types**: ONLY for defining data shapes and structures

### Interface Guidelines

**✅ Correct Usage - Abstractions:**
```typescript
// ✅ Interface for abstraction
export interface UserRepositoryInterface {
    save(user: User): Promise<void>;
    findById(id: string): Promise<User>;
}

// ✅ Interface for service contract
export interface HttpClientInterface {
    get<T>(url: string): Promise<T>;
    post<T>(url: string, data: any): Promise<T>;
}
```

**❌ Incorrect Usage - Data Types:**
```typescript
// ❌ DON'T use interface for data shapes
export interface LoginCredentials {
    email: string;
    password: string;
}

// ❌ DON'T use interface for response types
export interface AuthResponse {
    success: boolean;
    data: any;
}
```

### Type Guidelines

**✅ Correct Usage - Data Shapes:**
```typescript
// ✅ Use type for data structures
export type LoginCredentials = {
    email: string;
    password: string;
    turnstileToken?: string;
};

// ✅ Use type for API responses
export type AuthResponse = {
    success: boolean;
    message: string;
    data?: {
        token: string;
        user: UserData;
    };
};

// ✅ Use type for configuration objects
export type HttpClientConfig = {
    timeout?: number;
    retries?: number;
    baseURL?: string;
};
```

## File Organization Rules

### 1. Interfaces (Abstractions)
- **Location**: Each interface in its own file
- **Naming**: `[name]-interface.ts`
- **Examples**:
  - `user-repository.interface.ts`
  - `http-client.interface.ts`
  - `token-storage.interface.ts`

### 2. Types (Data Shapes)
- **Location**: Group related types in `.types.ts` files
- **Naming**: `[domain].types.ts`
- **Examples**:
  - `auth.types.ts` - Authentication-related types
  - `api.types.ts` - API request/response types
  - `user.types.ts` - User data types

### 3. File Structure Examples

```
domain/
├── repositories/
│   ├── user-repository.interface.ts
│   └── post-repository.interface.ts
├── types/
│   ├── user.types.ts
│   ├── auth.types.ts
│   └── api.types.ts
└── entities/
    └── user.entity.ts

infrastructure/
├── services/
│   ├── http-client.interface.ts
│   └── http-client.service.ts
├── storage/
│   ├── token-storage.interface.ts
│   └── local-token.storage.ts
└── types/
    └── infrastructure.types.ts
```

## Implementation Rules

### 1. Interface Files
```typescript
// user-repository.interface.ts
export interface UserRepositoryInterface {
    login(credentials: LoginCredentials): Promise<AuthResponse>;
    register(data: RegisterData): Promise<AuthResponse>;
    // ... other methods
}
```

### 2. Type Files
```typescript
// auth.types.ts
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
        user: UserData;
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
};
```

### 3. Implementation Files
```typescript
// http-user.repository.ts
import { UserRepositoryInterface } from '../repositories/user-repository.interface';
import { HttpClientInterface } from '../services/http-client.interface';
import { LoginCredentials, RegisterData, AuthResponse } from '../../domain/types/auth.types';

export class HttpUserRepository implements UserRepositoryInterface {
    constructor(private readonly httpClient: HttpClientInterface) {}

    async login(credentials: LoginCredentials): Promise<AuthResponse> {
        // Implementation
    }
}
```

## Benefits

### 1. Clear Separation
- **Interfaces**: Define contracts and behavior
- **Types**: Define data structure and shape

### 2. Better Organization
- Related types grouped together
- Interfaces focused on abstraction
- Easier to find and maintain

### 3. Import Clarity
```typescript
// Clear distinction in imports
import { UserRepositoryInterface } from './user-repository.interface';
import { LoginCredentials, AuthResponse } from '../types/auth.types';
```

### 4. TypeScript Advantages
- Interfaces support declaration merging
- Types support unions, intersections, and computed types
- Each used for their strengths

## Migration Strategy

When refactoring existing code:

1. **Identify mixed usage** - Find interfaces used for data types
2. **Extract types** - Move data shapes to `.types.ts` files
3. **Keep interfaces pure** - Only behavioral contracts
4. **Update imports** - Fix all import statements
5. **Verify functionality** - Ensure no breaking changes

## Authorization and Permissions Pattern

### Rule: Separate Authorization Logic from Entities

**✅ Correct Approach:**
```typescript
// Use service for authorization
const authService = new AuthorizationService();

// Check permissions
if (authService.can(user, PERMISSION.CREATE_POST)) {
    // Allow action
}

// Check roles
if (authService.is(user, ROLE.ADMIN)) {
    // Admin-specific logic
}
```

**❌ Incorrect Approach:**
```typescript
// DON'T hardcode methods in entities
if (user.canCreatePosts()) { ... }
if (user.isAdmin()) { ... }
```

### Benefits of Service-Based Authorization

1. **Single Responsibility**: Entities focus on data, services handle business logic
2. **Flexibility**: Easy to change permission rules without modifying entities
3. **Testability**: Authorization logic is isolated and testable
4. **Maintainability**: Constants for permissions/roles prevent typos
5. **Extensibility**: Easy to add new permissions without entity changes

### Permission Constants Pattern

```typescript
// permissions.types.ts
export const PERMISSION = {
    CREATE_POST: 'create_post',
    MANAGE_USERS: 'manage_users'
} as const;

export const ROLE = {
    ADMIN: 'admin',
    USER: 'user'
} as const;
```

## Enforcement

- **Code Review**: Check that interfaces are only abstractions
- **Authorization**: Use AuthorizationService instead of entity methods
- **Constants**: Use PERMISSION/ROLE constants instead of strings
- **Linting**: Configure ESLint rules if available
- **Documentation**: Reference this guide in PR templates
- **Examples**: Use this pattern in all new code