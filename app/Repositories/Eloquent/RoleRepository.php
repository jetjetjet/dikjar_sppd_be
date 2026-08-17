<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function getRolePermissionNames(int $roleId): array
    {
        return DB::table('role_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('role_id', $roleId)
            ->pluck('permissions.name')
            ->all();
    }

    public function guardOrFail(string $role): object
    {
        return $this->model->findOrCreate($role, 'sanctum');
    }

    public function searchByNamePattern(string $pattern): array
    {
        return $this->model->newQuery()
            ->whereRaw('UPPER(name) LIKE ?', ['%'.strtoupper($pattern).'%'])
            ->pluck('name')
            ->all();
    }
}
