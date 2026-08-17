<?php

namespace App\Services;

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
