<?php

namespace App\Services;

use App\Repositories\Contracts\PejabatTtdRepositoryInterface;

class PejabatTtdService
{
    public function __construct(protected PejabatTtdRepositoryInterface $pejabatRepository) {}

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

        $this->pejabatRepository->create([
            'pegawai_id' => $data['pegawai_id'],
            'autorisasi' => $data['autorisasi'],
            'anggaran_id' => $data['anggaran_id'] ?? null,
            'autorisasi_code' => $this->mapAutorisasi($data['autorisasi']),
            'is_active' => $data['is_active'] ?? '1',
        ]);
    }

    public function update(int $id, array $data): void
    {
        $this->pejabatRepository->updateById($id, [
            'pegawai_id' => $data['pegawai_id'],
            'autorisasi' => $data['autorisasi'],
            'anggaran_id' => $data['anggaran_id'] ?? null,
            'autorisasi_code' => $this->mapAutorisasi($data['autorisasi']),
        ]);
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
