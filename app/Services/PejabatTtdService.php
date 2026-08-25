<?php

namespace App\Services;

use App\Repositories\Contracts\PejabatTtdRepositoryInterface;
use Illuminate\Http\UploadedFile;

class PejabatTtdService
{
    public function __construct(
        protected PejabatTtdRepositoryInterface $pejabatRepository,
        protected FileStorageService $fileStorageService,
    ) {}

    public function getGrid()
    {
        $rows = $this->pejabatRepository->grid();

        return $rows->map(function ($row) {
            $row->status_aktif = $row->is_active ? 'Aktif' : 'Tidak Aktif';

            return $row;
        })->values();
    }

    public function search(?string $filter, ?int $anggaranId)
    {
        return $this->pejabatRepository->search($filter, $anggaranId);
    }

    public function show(int $id)
    {
        return $this->pejabatRepository->find($id);
    }

    public function store(array $data): void
    {
        $dup = $this->pejabatRepository->existsDuplicate((int) $data['pegawai_id'], $data['autorisasi']);
        if ($dup) {
            throw new \Exception('Pejabat sudah ada pada sistem.');
        }

        $payload = [
            'pegawai_id' => $data['pegawai_id'],
            'autorisasi' => $data['autorisasi'],
            'anggaran_id' => $data['anggaran_id'] ?? null,
            'autorisasi_code' => $this->mapAutorisasi($data['autorisasi']),
            'is_active' => $data['is_active'] ?? '1',
        ];

        if (isset($data['template']) && $data['template'] instanceof UploadedFile) {
            $payload['template_file_id'] = $this->fileStorageService->storeUploadedFile($data['template'], 'template-pejabat');
        }

        $this->pejabatRepository->create($payload);
    }

    public function update(int $id, array $data): void
    {
        $payload = [
            'pegawai_id' => $data['pegawai_id'],
            'autorisasi' => $data['autorisasi'],
            'anggaran_id' => $data['anggaran_id'] ?? null,
            'autorisasi_code' => $this->mapAutorisasi($data['autorisasi']),
        ];

        // Template lama SENGAJA dibiarkan di storage kalau diganti (tidak dihapus) —
        // lihat PLAN_TEMPLATE_PER_PEJABAT.md Keputusan #5. Kalau tidak ada file baru
        // di request ini, template_file_id existing tidak disentuh sama sekali.
        if (isset($data['template']) && $data['template'] instanceof UploadedFile) {
            $payload['template_file_id'] = $this->fileStorageService->storeUploadedFile($data['template'], 'template-pejabat');
        }

        $this->pejabatRepository->updateById($id, $payload);
    }

    public function setActive(int $id, bool $isActive): void
    {
        $this->pejabatRepository->updateById($id, ['is_active' => $isActive ? '1' : '0']);
    }

    public function destroy(int $id): void
    {
        $this->pejabatRepository->deleteById($id);
    }

    protected function mapAutorisasi(string $autorisasi): string
    {
        return match ($autorisasi) {
            'Pejabat Pelaksana Teknis Kegiatan' => 'PPTK',
            'Petugas Tanda Tangan' => 'PTTD',
            default => strtoupper($autorisasi),
        };
    }
}
