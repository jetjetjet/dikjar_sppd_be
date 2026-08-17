<?php

namespace App\Services;

use App\Repositories\Contracts\PegawaiRepositoryInterface;

class ProfileService
{
    public function __construct(
        protected PegawaiRepositoryInterface $pegawaiRepository,
        protected FileStorageService $fileStorageService,
    ) {}

    public function authorizeSelf(int $id): void
    {
        $pegawaiId = auth('sanctum')->user()->pegawai->id ?? null;
        if ((int) $id !== (int) $pegawaiId) {
            throw new \Exception('Tidak dapat mengubah user!');
        }
    }

    public function show(int $id)
    {
        $user = $this->pegawaiRepository->findOrFail($id);
        $user->role = auth('sanctum')->user()->getRoleNames()[0] ?? null;

        return $user;
    }

    public function update(int $id, array $data): void
    {
        $this->pegawaiRepository->updateById($id, [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
    }

    public function changePassword(int $id, string $password): void
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $this->pegawaiRepository->updateById($id, ['password' => bcrypt($password)]);
        }
    }

    public function changePhoto(int $id, mixed $file): string
    {
        if (! $file) {
            throw new \Exception('File tidak ditemukan.');
        }

        $path = $this->fileStorageService->storePath($file, 'profile');
        $this->pegawaiRepository->updateById($id, ['path_foto' => $path]);

        return $path;
    }
}
