import type { User } from '../entities/user.entity';
import type { PermissionType, RoleType } from '../types/permissions.types';

export interface AuthorizationServiceInterface {
    /**
     * Check if user has specific permission
     */
    can(user: User, permission: PermissionType): boolean;

    /**
     * Check if user has specific role
     */
    is(user: User, role: RoleType): boolean;

    /**
     * Check if user has any of the given permissions
     */
    canAny(user: User, permissions: PermissionType[]): boolean;

    /**
     * Check if user has all of the given permissions
     */
    canAll(user: User, permissions: PermissionType[]): boolean;

    /**
     * Get all permissions for a user
     */
    getPermissions(user: User): PermissionType[];

    /**
     * Check if user is active and can perform actions
     */
    isActive(user: User): boolean;
}