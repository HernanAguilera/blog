<?php

declare(strict_types=1);

namespace Blog\Domain\User\Repositories;

use Blog\Domain\User\Entities\User;
use Blog\Domain\User\ValueObjects\Email;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\User\ValueObjects\UserRole;

interface UserRepositoryInterface
{
    /**
     * Find a user by their unique identifier.
     */
    public function findById(UserId $id): ?User;

    /**
     * Find a user by their email address.
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Save a user (create or update).
     */
    public function save(User $user): User;

    /**
     * Delete a user by their identifier.
     */
    public function delete(UserId $id): bool;

    /**
     * Check if a user exists by email.
     */
    public function existsByEmail(Email $email): bool;

    /**
     * Find all users with pagination.
     *
     * @return array{users: User[], total: int, page: int, perPage: int}
     */
    public function findAll(int $page = 1, int $perPage = 15): array;

    /**
     * Find users by role.
     *
     * @return User[]
     */
    public function findByRole(UserRole $role, int $page = 1, int $perPage = 15): array;

    /**
     * Find active users.
     *
     * @return User[]
     */
    public function findActive(int $page = 1, int $perPage = 15): array;

    /**
     * Find inactive users.
     *
     * @return User[]
     */
    public function findInactive(int $page = 1, int $perPage = 15): array;

    /**
     * Find users with unverified emails.
     *
     * @return User[]
     */
    public function findUnverified(int $page = 1, int $perPage = 15): array;

    /**
     * Search users by name or email.
     *
     * @return User[]
     */
    public function search(string $query, int $page = 1, int $perPage = 15): array;

    /**
     * Count total users.
     */
    public function count(): int;

    /**
     * Count users by role.
     */
    public function countByRole(UserRole $role): int;

    /**
     * Count active users.
     */
    public function countActive(): int;

    /**
     * Get users created in a date range.
     *
     * @return User[]
     */
    public function findCreatedBetween(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $page = 1,
        int $perPage = 15
    ): array;

    /**
     * Get the next available user ID.
     */
    public function nextIdentity(): UserId;
}