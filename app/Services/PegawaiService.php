<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PegawaiService
{
    public function __construct(
        protected PegawaiRepositoryInterface $pegawaiRepository,
        protected FileStorageService $fileStorageService,
    ) {}

    public function getGrid()
    {
        return $this->pegawaiRepository->getAll();
    }

    public function search(array $filters, ?int $editId = null)
    {
        return $this->pegawaiRepository->search($filters, $editId);
    }

    public function show(int $id)
    {
        $pegawai = $this->pegawaiRepository->find($id);
        if ($pegawai && empty($pegawai->path_foto)) {
            $pegawai->path_foto = '/storage/profile/user.png';
        }

        return $pegawai;
    }

    public function store(array $data): void
    {
        DB::transaction(function () use ($data) {
            $pegawai = $this->pegawaiRepository->create([
                'nip' => $this->nullable($data['nip'] ?? null),
                'full_name' => $data['full_name'],
                'jabatan' => $data['jabatan'],
                'pangkat' => $this->nullable($data['pangkat'] ?? null),
                'golongan' => $this->nullable($data['golongan'] ?? null),
                'email' => $data['email'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'status_pegawai' => $data['status_pegawai'],
                'address' => $this->nullable($data['address'] ?? null),
                'phone' => $this->nullable($data['phone'] ?? null),
                'tgl_lahir' => isset($data['tgl_lahir']) ? json_decode($data['tgl_lahir']) ?? null : null,
                'pegawai_app' => '1',
            ]);

            if (! empty($data['pegawai_app'])) {
                User::create([
                    'email' => $data['email'],
                    'password' => bcrypt('password'),
                ]);
            }

            if (request()->hasFile('file') && request()->file('file') !== null) {
                $path = $this->fileStorageService->storePath(request()->file('file'), 'profile');
                $this->pegawaiRepository->updateById($pegawai->id, ['path_foto' => $path]);
            }
        });
    }

    public function update(int $id, array $data): void
    {
        $this->pegawaiRepository->updateById($id, [
            'nip' => $this->nullable($data['nip'] ?? null),
            'full_name' => $data['full_name'],
            'pangkat' => $this->nullable($data['pangkat'] ?? null),
            'golongan' => $this->nullable($data['golongan'] ?? null),
            'email' => $data['email'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'jabatan' => $data['jabatan'],
            'address' => $this->nullable($data['address'] ?? null),
            'phone' => $this->nullable($data['phone'] ?? null),
            'tgl_lahir' => isset($data['tgl_lahir']) ? json_decode($data['tgl_lahir']) ?? null : null,
            'status_pegawai' => $data['status_pegawai'],
        ]);
    }

    public function changePhoto(int $id): string
    {
        $file = request()->file('file');
        if (! $file) {
            throw new \Exception('File tidak ditemukan.');
        }

        $path = $this->fileStorageService->storePath($file, 'profile');

        $this->pegawaiRepository->updateById($id, ['path_foto' => $path]);

        return $path;
    }

    public function destroy(int $id): void
    {
        if ($id === 1) {
            throw new \Exception('Pegawai ini tidak dapat dihapus.');
        }

        $this->pegawaiRepository->deleteById($id);
    }

    protected function nullable($value)
    {
        return ($value === null || $value === 'null') ? null : $value;
    }
}
