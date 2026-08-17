<?php

namespace App\Repositories\Contracts;

interface InapRepositoryInterface extends BaseRepositoryInterface
{
    public function createInap(array $data): object;

    public function findScoped(int $id, int $pegawaiId, int $biayaId): ?object;

    public function activeByBiayaPegawai(int $biayaId, int $pegawaiId): ?object;
}
