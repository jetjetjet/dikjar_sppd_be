<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ReportSPPDRepositoryInterface extends BaseRepositoryInterface
{
    public function insertReport(array $data): void;

    public function getByPeriode(int $periode): Collection;

    public function batchForSaveReport(int $sptId): Collection;
}
