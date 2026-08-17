<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface PegawaiRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(): Collection;

    public function search(array $filters, ?int $editId = null): Collection;

    public function activeQuery(): Builder;
}
