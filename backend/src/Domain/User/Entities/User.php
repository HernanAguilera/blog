<?php

declare(strict_types=1);

namespace Blog\Domain\User\Entities;

use Blog\Domain\User\ValueObjects\Email;
use Blog\Domain\User\ValueObjects\Password;
use Blog\Domain\User\ValueObjects\Permission;
use Blog\Domain\User\ValueObjects\SocialProvider;
use Blog\Domain\User\ValueObjects\SocialUserData;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\User\ValueObjects\UserRole;
use DateTimeImmutable;

final class User
{
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $emailVerifiedAt = null;
    private ?string $socialId = null;
    private ?SocialProvider $socialProvider = null;

    public function __construct(
        private ?UserId $id,
        private string $name,
        private Email $email,
        private Password $password,
        private UserRole $role = UserRole::GUEST,
        private bool $isActive = true,
        ?DateTimeImmutable $createdAt = null,
        ?string $socialId = null,
        ?SocialProvider $socialProvider = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->socialId = $socialId;
        $this->socialProvider = $socialProvider;
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
        ?string $emailVerifiedAt = null,
        ?string $socialId = null,
        ?string $socialProvider = null
    ): self {
        $user = new self(
            id: $id ? new UserId($id) : null,
            name: $name,
            email: new Email($email),
            password: Password::fromHash($hashedPassword),
            role: UserRole::from($role),
            isActive: $isActive,
            createdAt: $createdAt ? new DateTimeImmutable($createdAt) : new DateTimeImmutable(),
            socialId: $socialId,
            socialProvider: $socialProvider ? SocialProvider::from($socialProvider) : null
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
            'social_id' => $this->socialId,
            'social_provider' => $this->socialProvider?->value,
        ];
    }

    public static function createFromSocial(SocialUserData $socialData): self
    {
        $user = new self(
            id: null,
            name: $socialData->getName(),
            email: new Email($socialData->getEmail()),
            password: Password::fromPlainText(bin2hex(random_bytes(32))), // Random password for OAuth users
            role: UserRole::GUEST,
            isActive: true,
            createdAt: new DateTimeImmutable(),
            socialId: $socialData->getSocialId(),
            socialProvider: $socialData->getProvider()
        );

        // OAuth users are auto-verified
        $user->emailVerifiedAt = new DateTimeImmutable();

        return $user;
    }

    public function linkSocialAccount(string $socialId, SocialProvider $provider): void
    {
        $this->socialId = $socialId;
        $this->socialProvider = $provider;
        $this->markAsUpdated();
    }

    public function unlinkSocialAccount(): void
    {
        $this->socialId = null;
        $this->socialProvider = null;
        $this->markAsUpdated();
    }

    public function hasSocialAccount(): bool
    {
        return $this->socialId !== null && $this->socialProvider !== null;
    }

    public function getSocialId(): ?string
    {
        return $this->socialId;
    }

    public function getSocialProvider(): ?SocialProvider
    {
        return $this->socialProvider;
    }

    public function isSocialUser(): bool
    {
        return $this->hasSocialAccount();
    }

    public function isLocalUser(): bool
    {
        return !$this->hasSocialAccount() || ($this->hasSocialAccount() && $this->password->getValue() !== null);
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}