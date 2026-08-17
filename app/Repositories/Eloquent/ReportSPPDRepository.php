<?php

namespace App\Repositories\Eloquent;

use App\Models\ReportSPPD;
use App\Repositories\Contracts\ReportSPPDRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReportSPPDRepository extends BaseRepository implements ReportSPPDRepositoryInterface
{
    public function __construct(ReportSPPD $model)
    {
        parent::__construct($model);
    }

    public function insertReport(array $data): void
    {
        $this->model->newQuery()->insert($data);
    }

    public function getByPeriode(int $periode): Collection
    {
        return $this->model->newQuery()->orderBy('id', 'DESC')->where('periode', $periode)->get();
    }

    public function batchForSaveReport(int $sptId): Collection
    {
        return $this->model->newQuery()->where('spt_id', $sptId)->get();
    }
}
