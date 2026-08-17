<?php

namespace App\Repositories\Contracts;

interface AuthRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?object;

    public function createToken(object $user, string $name, array $abilities): string;

    public function tokenAbilities(object $user): array;

    public function isSuperAdmin(object $user): bool;

    public function deleteCurrentToken(object $user): void;
}
