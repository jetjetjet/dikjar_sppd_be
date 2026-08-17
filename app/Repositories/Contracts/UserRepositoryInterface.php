<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function grid(): Collection;

    public function findByEmail(string $email): ?object;

    public function findUserRole(int $id): ?string;
}
