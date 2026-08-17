<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface SPTRepositoryInterface extends BaseRepositoryInterface
{
    public function getGrid(?string $tahun, ?int $loginId, bool $isAdmin): Collection;

    public function gridIndex(?string $tahun, ?int $loginId, bool $isAdmin): Builder;

    public function findWithDetail(int $id): mixed;

    public function getByPeriode(string $tahun): int;

    public function getFile(int $id): ?object;

    public function findWithFile(int $id): ?object;

    public function getByAnggaranRealisasi(): Builder;

    public function allocateNoIndex(string $tahun): int;

    public function countActiveByAnggaranId(int $anggaranId): int;
}
