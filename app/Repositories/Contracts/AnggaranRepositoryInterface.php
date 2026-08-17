<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface AnggaranRepositoryInterface extends BaseRepositoryInterface
{
    public function grid(int $roleId, bool $isAdmin, ?string $role): Collection;

    public function getGridQuery(bool $isAdmin, ?string $role): Builder;

    public function search(int $roleId, bool $isAdmin, ?string $role): Collection;

    public function getSearchQuery(bool $isAdmin, ?string $role): Builder;
}
