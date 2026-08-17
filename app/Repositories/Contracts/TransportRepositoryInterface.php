<?php

namespace App\Repositories\Contracts;

interface TransportRepositoryInterface extends BaseRepositoryInterface
{
    public function createTransport(array $data): object;

    public function findScoped(int $id, int $pegawaiId, int $biayaId): ?object;

    public function flightByJourney(int $biayaId, int $pegawaiId, string $perjalanan): ?object;
}
