export enum UserRoleEnum {
    SUPER_ADMIN = 'super_admin',
    ADMIN = 'admin',
    COLLABORATOR = 'collaborator',
    GUEST = 'guest'
}

export class UserRole {
    private readonly _value: UserRoleEnum;

    constructor(value: string | UserRoleEnum) {
        const normalizedValue = typeof value === 'string' ? value.toLowerCase() : value;

        if (!Object.values(UserRoleEnum).includes(normalizedValue as UserRoleEnum)) {
            throw new Error(`Invalid user role: ${value}`);
        }

        this._value = normalizedValue as UserRoleEnum;
    }

    public value(): string {
        return this._value;
    }

    public equals(other: UserRole): boolean {
        return this._value === other._value;
    }

    // Basic role validation
    public is(role: UserRoleEnum | string): boolean {
        return this._value === role;
    }

    // Note: For complex permission checks, use AuthorizationService
    // This keeps the Value Object focused on its core responsibility

    public toString(): string {
        return this._value;
    }

    // Static factory methods
    public static superAdmin(): UserRole {
        return new UserRole(UserRoleEnum.SUPER_ADMIN);
    }

    public static admin(): UserRole {
        return new UserRole(UserRoleEnum.ADMIN);
    }

    public static collaborator(): UserRole {
        return new UserRole(UserRoleEnum.COLLABORATOR);
    }

    public static guest(): UserRole {
        return new UserRole(UserRoleEnum.GUEST);
    }
}