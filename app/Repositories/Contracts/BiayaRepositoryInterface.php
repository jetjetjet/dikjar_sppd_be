<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface BiayaRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySptAndPegawai(int $sptId, int $pegawaiId): ?object;

    public function sumBySpt(int $sptId): float;

    public function deleteBySptId(int $sptId): void;

    public function createForSpt(int $sptId, int $anggaranId, int $pegawaiId): object;

    public function recalculate(int $biayaId): void;

    public function gridRows(int $biayaId, int $pegawaiId): array;

    public function findByAnggaranSum(int $anggaranId): float;

    public function getByAnggaranGrouped(): Builder;
}
