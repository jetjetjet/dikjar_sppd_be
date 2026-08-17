<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PejabatTtdRepositoryInterface extends BaseRepositoryInterface
{
    public function grid(): Collection;

    public function search(?string $filter, ?int $anggaranId): Collection;

    public function existsDuplicate(int $pegawaiId, string $autorisasi): bool;
}
