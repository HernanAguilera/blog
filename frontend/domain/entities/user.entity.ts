import { UserRole } from '../value-objects/user-role.vo';
import { UserId } from '../value-objects/user-id.vo';
import { UserEmail } from '../value-objects/user-email.vo';
import type { UserData } from '../types/auth.types';

export class User {
    constructor(
        private readonly id: UserId,
        private readonly name: string,
        private readonly email: UserEmail,
        private readonly role: UserRole,
        private readonly isActive: boolean = true,
        private readonly emailVerified: boolean = false,
        private readonly createdAt?: Date,
        private readonly updatedAt?: Date
    ) {
        this.validateName(name);
    }

    private validateName(name: string): void {
        if (!name || name.trim() === '') {
            throw new Error('User name cannot be empty');
        }

        if (name.trim().length < 2) {
            throw new Error('User name must be at least 2 characters long');
        }

        if (name.trim().length > 100) {
            throw new Error('User name cannot exceed 100 characters');
        }
    }

    // Getters
    public getId(): UserId {
        return this.id;
    }

    public getName(): string {
        return this.name;
    }

    public getEmail(): UserEmail {
        return this.email;
    }

    public getRole(): UserRole {
        return this.role;
    }

    public getIsActive(): boolean {
        return this.isActive;
    }

    public getEmailVerified(): boolean {
        return this.emailVerified;
    }

    public getCreatedAt(): Date | undefined {
        return this.createdAt;
    }

    public getUpdatedAt(): Date | undefined {
        return this.updatedAt;
    }

    // Business methods - Basic role check (for backward compatibility)
    public hasRole(role: string): boolean {
        return this.role.value() === role;
    }

    // Note: For permissions and role-based authorization, use AuthorizationService
    // Example: authService.can(user, PERMISSION.CREATE_POST)
    // Example: authService.is(user, ROLE.ADMIN)

    // Business methods - Status checks
    public isActiveUser(): boolean {
        return this.isActive;
    }

    public hasVerifiedEmail(): boolean {
        return this.emailVerified;
    }

    public requiresEmailVerification(): boolean {
        return !this.emailVerified && this.isActive;
    }

    // Utility methods
    public getDisplayName(): string {
        return this.name.trim();
    }

    public getInitials(): string {
        return this.name
            .trim()
            .split(' ')
            .map(part => part.charAt(0).toUpperCase())
            .slice(0, 2)
            .join('');
    }

    public equals(other: User): boolean {
        return this.id.equals(other.id);
    }

    // Serialization methods
    public toPlainObject(): UserData {
        return {
            id: this.id.value(),
            name: this.name,
            email: this.email.value(),
            role: this.role.value(),
            isActive: this.isActive,
            emailVerified: this.emailVerified,
            createdAt: this.createdAt?.toISOString(),
            updatedAt: this.updatedAt?.toISOString()
        };
    }

    public toJSON(): UserData {
        return this.toPlainObject();
    }

    // Factory methods
    public static fromApiResponse(data: any): User {
        if (!data) {
            throw new Error('Cannot create User from null or undefined data');
        }

        return new User(
            new UserId(data.id),
            data.name,
            new UserEmail(data.email),
            new UserRole(data.role),
            data.is_active ?? data.isActive ?? true,
            data.email_verified ?? data.emailVerified ?? false,
            data.created_at ? new Date(data.created_at) : undefined,
            data.updated_at ? new Date(data.updated_at) : undefined
        );
    }

    public static create(
        id: string,
        name: string,
        email: string,
        role: string,
        isActive: boolean = true,
        emailVerified: boolean = false
    ): User {
        return new User(
            new UserId(id),
            name,
            new UserEmail(email),
            new UserRole(role),
            isActive,
            emailVerified,
            new Date(),
            new Date()
        );
    }
}