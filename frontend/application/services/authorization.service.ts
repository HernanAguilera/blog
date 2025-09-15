import type { User } from '../../domain/entities/user.entity';
import type { AuthorizationServiceInterface } from '../../domain/services/authorization.interface';
import type { PermissionType, RoleType } from '../../domain/types/permissions.types';
import { ROLE_PERMISSIONS } from '../../domain/types/permissions.types';

export class AuthorizationService implements AuthorizationServiceInterface {
    can(user: User, permission: PermissionType): boolean {
        if (!this.isActive(user)) {
            return false;
        }

        const userRole = user.getRole().value() as RoleType;
        const rolePermissions = ROLE_PERMISSIONS[userRole] || [];

        return rolePermissions.includes(permission);
    }

    is(user: User, role: RoleType): boolean {
        return user.getRole().value() === role;
    }

    canAny(user: User, permissions: PermissionType[]): boolean {
        return permissions.some(permission => this.can(user, permission));
    }

    canAll(user: User, permissions: PermissionType[]): boolean {
        return permissions.every(permission => this.can(user, permission));
    }

    getPermissions(user: User): PermissionType[] {
        if (!this.isActive(user)) {
            return [];
        }

        const userRole = user.getRole().value() as RoleType;
        return ROLE_PERMISSIONS[userRole] || [];
    }

    isActive(user: User): boolean {
        return user.getIsActive() && user.getEmailVerified();
    }
}