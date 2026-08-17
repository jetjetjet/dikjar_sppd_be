<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function getGrid()
    {
        return $this->userRepository->grid();
    }

    public function show(int $id)
    {
        $user = $this->userRepository->findOrFail($id);
        $user->role = $user->getRoleNames()[0] ?? null;
        $user->full_name = $user->pegawai->full_name ?? null;

        return $user;
    }

    public function store(array $data): void
    {
        $user = $this->userRepository->create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->assignRole($data['role']);
    }

    public function update(int $id, array $data): void
    {
        $user = $this->userRepository->findOrFail($id);
        $user->syncRoles([$data['role']]);

        if (! empty($data['password'])) {
            $this->userRepository->updateById($id, ['password' => bcrypt($data['password'])]);
        }
    }

    public function changePassword(int $id, string $password): void
    {
        $this->userRepository->updateById($id, ['password' => bcrypt($password)]);
    }

    public function destroy(int $id): void
    {
        if ($id === 1) {
            throw new \Exception('User ini tidak dapat dihapus.');
        }

        $this->userRepository->deleteById($id);
    }
}
