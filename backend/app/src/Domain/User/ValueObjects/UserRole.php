<?php

declare(strict_types=1);

namespace App\src\Domain\User\ValueObjects;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case COLLABORATOR = 'collaborator';
    case GUEST = 'guest';

    public function getPermissions(): array
    {
        return match($this) {
            self::SUPER_ADMIN => [
                Permission::MANAGE_USERS,
                Permission::MANAGE_POSTS,
                Permission::MANAGE_COMMENTS,
                Permission::MANAGE_SETTINGS,
                Permission::MANAGE_SYSTEM,
                Permission::MODERATE_COMMENTS,
                Permission::VIEW_ANALYTICS,
                Permission::MANAGE_NEWSLETTER,
                Permission::MANAGE_MEDIA,
                Permission::MANAGE_PAGES,
            ],
            self::ADMIN => [
                Permission::MANAGE_POSTS,
                Permission::MANAGE_COMMENTS,
                Permission::MODERATE_COMMENTS,
                Permission::VIEW_ANALYTICS,
                Permission::MANAGE_NEWSLETTER,
                Permission::UPLOAD_MEDIA,
                Permission::CREATE_PAGES,
                Permission::EDIT_PAGES,
            ],
            self::COLLABORATOR => [
                Permission::CREATE_POSTS,
                Permission::EDIT_OWN_POSTS,
                Permission::VIEW_OWN_ANALYTICS,
                Permission::UPLOAD_MEDIA,
                Permission::CREATE_COMMENTS,
            ],
            self::GUEST => [
                Permission::READ_POSTS,
                Permission::CREATE_COMMENTS,
            ]
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
            self::COLLABORATOR => 'Colaborador',
            self::GUEST => 'Invitado'
        };
    }

    public function hasPermission(Permission $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }

    public function can(Permission $permission): bool
    {
        return $this->hasPermission($permission);
    }

    public function isHigherThan(UserRole $other): bool
    {
        $hierarchy = [
            self::GUEST->value => 0,
            self::COLLABORATOR->value => 1,
            self::ADMIN->value => 2,
            self::SUPER_ADMIN->value => 3,
        ];

        return $hierarchy[$this->value] > $hierarchy[$other->value];
    }
}