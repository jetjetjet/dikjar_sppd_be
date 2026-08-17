<?php

namespace App\Repositories\Eloquent;

use App\Models\Pengeluaran;
use App\Repositories\Contracts\PengeluaranRepositoryInterface;

class PengeluaranRepository extends BaseRepository implements PengeluaranRepositoryInterface
{
    public function __construct(Pengeluaran $model)
    {
        parent::__construct($model);
    }

    public function createPengeluaran(array $data): object
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

    public function sumTotalByBiayaPegawai(int $biayaId, int $pegawaiId): float
    {
        return (float) $this->model->newQuery()
            ->where('biaya_id', $biayaId)
            ->where('pegawai_id', $pegawaiId)
            ->sum('total');
    }
}
