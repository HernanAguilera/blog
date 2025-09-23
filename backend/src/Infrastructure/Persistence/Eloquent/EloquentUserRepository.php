<?php

declare(strict_types=1);

namespace Blog\Infrastructure\Persistence\Eloquent;

use Blog\Domain\User\Repositories\UserRepositoryInterface;
use Blog\Domain\User\Entities\User as DomainUser;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\User\ValueObjects\Email;
use Blog\Domain\User\ValueObjects\Password;
use Blog\Domain\User\ValueObjects\UserRole;
use App\Models\User as EloquentUser;
use DateTimeImmutable;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(UserId $id): ?DomainUser
    {
        $eloquentUser = EloquentUser::find($id->value());

        return $eloquentUser ? $this->toDomainEntity($eloquentUser) : null;
    }

    public function findByEmail(Email $email): ?DomainUser
    {
        $eloquentUser = EloquentUser::where('email', $email->value())->first();

        return $eloquentUser ? $this->toDomainEntity($eloquentUser) : null;
    }

    public function save(DomainUser $user): DomainUser
    {
        // Para nuevos usuarios: buscar si existe en BD, sino crear nuevo
        $eloquentUser = null;
        if ($user->getId() && $user->getId()->value() > 0) {
            $eloquentUser = EloquentUser::find($user->getId()->value());
        }

        if (!$eloquentUser) {
            $eloquentUser = new EloquentUser();
        }

        $eloquentUser->fill([
            'name' => $user->getName(),
            'email' => $user->getEmail()->value(),
            'password' => $user->getPassword()->value(),
            'role' => $user->getRole()->value,
            'is_active' => $user->isActive(),
            'email_verified_at' => $user->isEmailVerified() ? now() : null,
        ]);

        $eloquentUser->save();

        return $this->toDomainEntity($eloquentUser);
    }

    public function delete(UserId $id): bool
    {
        return EloquentUser::destroy($id->value()) > 0;
    }

    public function existsByEmail(Email $email): bool
    {
        return EloquentUser::where('email', $email->value())->exists();
    }

    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $eloquentUsers = EloquentUser::offset($offset)->limit($perPage)->get();
        $total = EloquentUser::count();

        return [
            'users' => $eloquentUsers->map(fn($user) => $this->toDomainEntity($user))->toArray(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    public function findByRole(UserRole $role, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $eloquentUsers = EloquentUser::where('role', $role->value)
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return $eloquentUsers->map(fn($user) => $this->toDomainEntity($user))->toArray();
    }

    public function findActive(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $eloquentUsers = EloquentUser::where('is_active', true)
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return $eloquentUsers->map(fn($user) => $this->toDomainEntity($user))->toArray();
    }

    public function findInactive(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $eloquentUsers = EloquentUser::where('is_active', false)
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return $eloquentUsers->map(fn($user) => $this->toDomainEntity($user))->toArray();
    }

    public function findUnverified(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $eloquentUsers = EloquentUser::whereNull('email_verified_at')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return $eloquentUsers->map(fn($user) => $this->toDomainEntity($user))->toArray();
    }

    public function search(string $query, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $eloquentUsers = EloquentUser::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return $eloquentUsers->map(fn($user) => $this->toDomainEntity($user))->toArray();
    }

    public function count(): int
    {
        return EloquentUser::count();
    }

    public function countByRole(UserRole $role): int
    {
        return EloquentUser::where('role', $role->value)->count();
    }

    public function countActive(): int
    {
        return EloquentUser::where('is_active', true)->count();
    }

    public function findCreatedBetween(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $page = 1,
        int $perPage = 15
    ): array {
        $offset = ($page - 1) * $perPage;
        $eloquentUsers = EloquentUser::whereBetween('created_at', [$from, $to])
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return $eloquentUsers->map(fn($user) => $this->toDomainEntity($user))->toArray();
    }

    public function nextIdentity(): UserId
    {
        $maxId = EloquentUser::max('id') ?? 0;
        return new UserId($maxId + 1);
    }

    private function toDomainEntity(EloquentUser $eloquentUser): DomainUser
    {
        return DomainUser::fromPrimitives(
            id: $eloquentUser->id,
            name: $eloquentUser->name,
            email: $eloquentUser->email,
            hashedPassword: $eloquentUser->password,
            role: $eloquentUser->role,
            isActive: (bool) $eloquentUser->is_active,
            createdAt: $eloquentUser->created_at->toDateTimeString(),
            updatedAt: $eloquentUser->updated_at->toDateTimeString(),
            emailVerifiedAt: $eloquentUser->email_verified_at?->toDateTimeString()
        );
    }
}