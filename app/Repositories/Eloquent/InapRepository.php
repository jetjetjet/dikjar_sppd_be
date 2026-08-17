<?php

namespace App\Repositories\Eloquent;

use App\Models\Inap;
use App\Repositories\Contracts\InapRepositoryInterface;

class InapRepository extends BaseRepository implements InapRepositoryInterface
{
    public function __construct(Inap $model)
    {
        parent::__construct($model);
    }

    public function createInap(array $data): object
    {
        return $this->model->newQuery()->create($data);
    }

    public function findScoped(int $id, int $pegawaiId, int $biayaId): ?object
    {
        return $this->model->newQuery()
            ->where('id', $id)
            ->where('pegawai_id', $pegawaiId)
            ->where('biaya_id', $biayaId)
            ->first();
    }

    public function activeByBiayaPegawai(int $biayaId, int $pegawaiId): ?object
    {
        return $this->model->newQuery()
            ->where('biaya_id', $biayaId)
            ->where('pegawai_id', $pegawaiId)
            ->first();
    }
}
