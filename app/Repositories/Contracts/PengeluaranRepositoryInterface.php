<?php

namespace App\Repositories\Contracts;

interface PengeluaranRepositoryInterface extends BaseRepositoryInterface
{
    public function createPengeluaran(array $data): object;

    public function findScoped(int $id, int $pegawaiId, int $biayaId): ?object;

    public function sumTotalByBiayaPegawai(int $biayaId, int $pegawaiId): float;
}
