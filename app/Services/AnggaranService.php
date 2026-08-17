<?php

namespace App\Services;

use App\Repositories\Contracts\AnggaranRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\SPTRepositoryInterface;

class AnggaranService
{
    public function __construct(
        protected AnggaranRepositoryInterface $anggaranRepository,
        protected SPTRepositoryInterface $sptRepository,
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function grid()
    {
        $role = auth('sanctum')->user()->roles->pluck('name')[0] ?? null;
        $isAdmin = auth('sanctum')->user()->tokenCan('is_admin');

        if (! $isAdmin && $role === null) {
            throw new \Exception('Anggaran tidak ditemukan.');
        }

        $rows = $this->anggaranRepository->grid(0, $isAdmin, $role);

        return $rows->map(function ($row) {
            $pagu = (float) $row->pagu;
            $realisasi = (float) ($row->realisasi ?? 0);

            return (object) [
                'id' => $row->id,
                'kode_rekening' => $row->kode_rekening,
                'nama_rekening' => $row->nama_rekening,
                'uraian' => $row->uraian,
                'pagu' => number_format($pagu),
                'realisasi' => number_format($realisasi),
                'sisa' => number_format($pagu - $realisasi),
                'periode' => $row->periode,
            ];
        });
    }

    public function search()
    {
        $role = auth('sanctum')->user()->roles->pluck('name')[0] ?? null;
        $isAdmin = auth('sanctum')->user()->tokenCan('is_admin');

        if (! $isAdmin && $role === null) {
            throw new \Exception('Anggaran tidak ditemukan.');
        }

        $rows = $this->anggaranRepository->search(0, $isAdmin, $role);

        return $rows->map(function ($row) {
            $sisa = ((float) $row->pagu) - (float) ($row->realisasi ?? 0);

            return (object) [
                'id' => $row->id,
                'kode_rekening' => $row->kode_rekening,
                'nama_rekening' => $row->nama_rekening,
                'uraian' => $row->uraian,
                'pagu' => 'Rp '.number_format((float) $row->pagu),
                'sisa' => 'Rp '.number_format($sisa),
                'pptk_name' => $row->pptk_name ?? null,
                'pptk_id' => $row->pptk_id ?? null,
                'bendahara_name' => $row->bendahara_name ?? null,
                'bendahara_id' => $row->bendahara_id ?? null,
                'pengguna_name' => $row->pengguna_name ?? null,
                'pengguna_id' => $row->pengguna_id ?? null,
            ];
        });
    }

    public function show(int $id)
    {
        return $this->anggaranRepository->find($id);
    }

    public function store(array $data): void
    {
        $this->anggaranRepository->create([
            'kode_rekening' => $data['kode_rekening'],
            'nama_rekening' => $data['nama_rekening'],
            'bidang' => $data['bidang'],
            'uraian' => $data['uraian'] ?? null,
            'pagu' => $data['pagu'],
            'periode' => $data['periode'],
            'bendahara_id' => $data['bendahara_id'] ?? null,
            'pptk_id' => $data['pptk_id'] ?? null,
            'pengguna_id' => $data['pengguna_id'] ?? null,
        ]);
    }

    public function update(int $id, array $data): void
    {
        $this->anggaranRepository->updateById($id, [
            'kode_rekening' => $data['kode_rekening'],
            'nama_rekening' => $data['nama_rekening'],
            'bidang' => $data['bidang'],
            'uraian' => $data['uraian'] ?? null,
            'pagu' => $data['pagu'],
            'periode' => $data['periode'],
            'bendahara_id' => $data['bendahara_id'] ?? null,
            'pptk_id' => $data['pptk_id'] ?? null,
            'pengguna_id' => $data['pengguna_id'] ?? null,
        ]);
    }

    public function destroy(int $id): void
    {
        if ($this->sptRepository->countActiveByAnggaranId($id) > 0) {
            throw new \Exception('Tidak dapat menghapus anggaran yang sedang berjalan!');
        }

        $this->anggaranRepository->deleteById($id);
    }

    public function searchRole(): array
    {
        return $this->roleRepository->searchByNamePattern('STAF');
    }
}
