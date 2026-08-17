<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;

class RoleService
{
    public function __construct(protected RoleRepositoryInterface $roleRepository) {}

    public function getGrid()
    {
        return $this->roleRepository->getAll();
    }

    public function getPermission(): array
    {
        $perms = Permission::all()->pluck('name')->all();

        $grouped = [];
        foreach ($perms as $perm) {
            $parts = explode('-', $perm, 2);
            $moduleKey = strtoupper($parts[0]);
            $module = ucwords($parts[0]);
            $action = $parts[1] ?? '';

            $grouped[$moduleKey]['module'] = $module;
            $grouped[$moduleKey]['actions'][] = [
                'raw' => $action,
                'value' => $perm,
                'active' => false,
            ];
        }

        ksort($grouped);

        return $grouped;
    }

    public function store(string $name, array $perms): void
    {
        $role = $this->roleRepository->guardOrFail($name);
        $role->givePermissionTo($perms);
    }

    public function show(int $id): array
    {
        $role = $this->roleRepository->findOrFail($id);
        $hasPermission = $this->roleRepository->getRolePermissionNames($id);

        $allPerms = Permission::all()->pluck('name')->all();
        $grouped = [];
        foreach ($allPerms as $perm) {
            $parts = explode('-', $perm, 2);
            $moduleKey = strtoupper($parts[0]);
            $module = ucwords($parts[0]);
            $action = $parts[1] ?? '';

            $grouped[$moduleKey]['module'] = $module;
            $grouped[$moduleKey]['actions'][] = [
                'raw' => $action,
                'value' => $perm,
                'active' => in_array($perm, $hasPermission, true),
            ];
        }
        ksort($grouped);

        $role->user = $role->id != null ? $role->users()->get() : [];

        return ['header' => $role, 'perms' => $grouped];
    }

    public function update(int $id, string $name, array $perms): void
    {
        $role = $this->roleRepository->findOrFail($id);
        $role->syncPermissions($perms);
        $role->update(['name' => $name]);
    }

    public function destroy(int $id): void
    {
        if ($id === 1) {
            throw new \Exception('Peran ini tidak dapat dihapus.');
        }

        $this->roleRepository->deleteById($id);
    }

    public function selectedPerms(array $perms): array
    {
        $selected = [];
        foreach ($perms as $group) {
            foreach (($group['actions'] ?? []) as $action) {
                if (! empty($action['active'])) {
                    $selected[] = $action['value'];
                }
            }
        }

        return array_values(array_filter($selected));
    }
}
