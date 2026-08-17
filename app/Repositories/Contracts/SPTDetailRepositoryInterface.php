<?php

namespace App\Repositories\Contracts;

interface SPTDetailRepositoryInterface extends BaseRepositoryInterface
{
    public function createDetail(int $sptId, int $pegawaiId, bool $isPelaksana = false): void;

    public function getBySptId(int $sptId): object;

    public function getPegawaiIds(int $sptId, bool $onlyNonPelaksana = false): object;

    public function syncPegawai(int $sptId, array $pegawaiIds, ?int $pelaksanaId): void;

    public function getUsersForProses(int $sptId): object;

    public function hasPelaksana(int $sptId): bool;

    public function getFirstBySptAndPegawai(int $sptId, int $pegawaiId): ?object;

    public function updateFile(int $sptId, int $pegawaiId, int $fileId): void;

    public function deleteBySptId(int $sptId): void;

    public function updateSettledBySptId(int $sptId, string $settledBy): void;
}
