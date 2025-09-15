<?php

declare(strict_types=1);

namespace App\src\Domain\User\Entities;

use App\src\Domain\User\ValueObjects\Email;
use App\src\Domain\User\ValueObjects\Password;
use App\src\Domain\User\ValueObjects\Permission;
use App\src\Domain\User\ValueObjects\UserId;
use App\src\Domain\User\ValueObjects\UserRole;
use DateTimeImmutable;

final class User
{
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $emailVerifiedAt = null;

    public function __construct(
        private ?UserId $id,
        private string $name,
        private Email $email,
        private Password $password,
        private UserRole $role = UserRole::GUEST,
        private bool $isActive = true,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }


    public static function create(
        string $name,
        Email $email,
        Password $password,
        UserRole $role = UserRole::GUEST
    ): self {
        return new self(
            id: null,
            name: $name,
            email: $email,
            password: $password,
            role: $role
        );
    }

    public static function fromPrimitives(
        ?int $id,
        string $name,
        string $email,
        string $hashedPassword,
        string $role,
        bool $isActive = true,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?string $emailVerifiedAt = null
    ): self {
        $user = new self(
            id: $id ? new UserId($id) : null,
            name: $name,
            email: new Email($email),
            password: Password::fromHash($hashedPassword),
            role: UserRole::from($role),
            isActive: $isActive,
            createdAt: $createdAt ? new DateTimeImmutable($createdAt) : new DateTimeImmutable()
        );

        if ($updatedAt) {
            $user->updatedAt = new DateTimeImmutable($updatedAt);
        }

        if ($emailVerifiedAt) {
            $user->emailVerifiedAt = new DateTimeImmutable($emailVerifiedAt);
        }

        return $user;
    }

    public function getId(): ?UserId
    {
        return $this->id;
    }

    public function setId(UserId $id): void
    {
        if ($this->id !== null) {
            throw new \LogicException('User ID cannot be changed once set');
        }
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->markAsUpdated();
    }

    public function updateEmail(Email $email): void
    {
        if (!$this->email->equals($email)) {
            $this->email = $email;
            $this->emailVerifiedAt = null; // Reset verification when email changes
            $this->markAsUpdated();
        }
    }

    public function updatePassword(Password $password): void
    {
        $this->password = $password;
        $this->markAsUpdated();
    }

    public function changeRole(UserRole $role): void
    {
        $this->role = $role;
        $this->markAsUpdated();
    }

    public function activate(): void
    {
        if (!$this->isActive) {
            $this->isActive = true;
            $this->markAsUpdated();
        }
    }

    public function deactivate(): void
    {
        if ($this->isActive) {
            $this->isActive = false;
            $this->markAsUpdated();
        }
    }

    public function verifyEmail(): void
    {
        if ($this->emailVerifiedAt === null) {
            $this->emailVerifiedAt = new DateTimeImmutable();
            $this->markAsUpdated();
        }
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return $this->password->verify($plainPassword);
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->role->hasPermission($permission);
    }

    public function can(Permission $permission): bool
    {
        return $this->hasPermission($permission);
    }

    public function canManageUser(User $targetUser): bool
    {
        // Super admin can manage everyone
        if ($this->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // Users cannot manage themselves through this method
        if ($this->id && $targetUser->id && $this->id->equals($targetUser->id)) {
            return false;
        }

        // Higher roles can manage lower roles
        return $this->role->isHigherThan($targetUser->role);
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id?->value(),
            'name' => $this->name,
            'email' => $this->email->value(),
            'role' => $this->role->value,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'email_verified_at' => $this->emailVerifiedAt?->format('Y-m-d H:i:s'),
        ];
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}