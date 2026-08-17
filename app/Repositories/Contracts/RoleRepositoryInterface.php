<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(): Collection;

    public function getRolePermissionNames(int $roleId): array;

    public function guardOrFail(string $role): object;

    public function searchByNamePattern(string $pattern): array;
}
