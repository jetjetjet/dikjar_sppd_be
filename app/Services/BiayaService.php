<?php

namespace App\Services;

use App\Exceptions\BiayaLockedException;
use App\Models\SPT;
use App\Repositories\Contracts\BiayaRepositoryInterface;
use App\Repositories\Contracts\FileRepositoryInterface;

class BiayaService
{
    public function __construct(
        protected BiayaRepositoryInterface $biayaRepository,
        protected FileStorageService $fileStorageService,
        protected FileRepositoryInterface $fileRepository,
    ) {}

    /**
     * Cegah tambah/ubah/hapus/upload struk biaya (transport/inap/pengeluaran) kalau
     * SPT terkait sudah di-void atau sudah settled. Dipanggil dari Transport/Inap/
     * PengeluaranService — satu-satunya penegakan di server, karena tombol di FE
     * cuma menyembunyikan aksi (bisa dilewati lewat panggilan API langsung).
     */
    public function assertEditable(int $biayaId): void
    {
        $biaya = $this->biayaRepository->find($biayaId);

        if (! $biaya) {
            throw new BiayaLockedException('Data biaya tidak ditemukan.');
        }

        $spt = SPT::find($biaya->spt_id);

        if ($spt && $spt->voided_at) {
            throw new BiayaLockedException('SPT sudah di-void, tidak bisa mengubah data biaya.');
        }

        if ($spt && $spt->settled_at) {
            throw new BiayaLockedException('SPPD sudah settled, tidak bisa mengubah data biaya.');
        }
    }

    /**
     * Centrally recalculate a biaya row from its child tables.
     * Used by Transport/Inap/Pengeluaran to avoid manual arithmetic drift.
     */
    public function recalculate(int $biayaId): void
    {
        $this->biayaRepository->recalculate($biayaId);
    }

    public function getGrid(int $biayaId, int $pegawaiId): array
    {
        $rows = $this->biayaRepository->gridRows($biayaId, $pegawaiId);

        $groups = [];
        foreach ($rows as $r) {
            $groups[$r->tipe]['tipe'] ??= $r->tipe;
            $groups[$r->tipe]['biaya'] = ($groups[$r->tipe]['biaya'] ?? 0) + $r->biaya;
            $groups[$r->tipe]['children'][] = $r;
        }

        return array_values($groups);
    }

    public function store(array $data): int
    {
        return (int) $this->biayaRepository->create([
            'pegawai_id' => $data['pegawai_id'],
            'spt_id' => $data['spt_id'],
        ])->id;
    }
}
