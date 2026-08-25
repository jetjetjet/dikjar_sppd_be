<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PejabatTtdRepositoryInterface extends BaseRepositoryInterface
{
    public function grid(): Collection;

    public function search(?string $filter, ?int $anggaranId): Collection;

    public function existsDuplicate(int $pegawaiId, string $autorisasi): bool;

    /**
     * Cari pejabat aktif berdasarkan pegawai_id + autorisasi_code — dipakai
     * DocumentGeneratorService untuk resolve template Word per pejabat.
     */
    public function findByPegawaiAndAutorisasi(int $pegawaiId, string $autorisasiCode): ?object;
}
