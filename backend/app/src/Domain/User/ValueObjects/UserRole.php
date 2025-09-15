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
                'manage_users',
                'manage_posts',
                'manage_comments',
                'manage_settings',
                'manage_system',
                'moderate_content',
                'view_analytics'
            ],
            self::ADMIN => [
                'manage_posts',
                'manage_comments',
                'moderate_content',
                'view_analytics'
            ],
            self::COLLABORATOR => [
                'create_posts',
                'edit_own_posts',
                'view_own_analytics'
            ],
            self::GUEST => [
                'read_posts',
                'create_comments'
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

    public function canManageUsers(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    public function canManagePosts(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN]);
    }

    public function canModerateComments(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN]);
    }

    public function canCreatePosts(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN, self::COLLABORATOR]);
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