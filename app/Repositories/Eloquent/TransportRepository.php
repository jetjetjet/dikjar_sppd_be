<?php

namespace App\Repositories\Eloquent;

use App\Models\Transport;
use App\Repositories\Contracts\TransportRepositoryInterface;

class TransportRepository extends BaseRepository implements TransportRepositoryInterface
{
    public function __construct(Transport $model)
    {
        parent::__construct($model);
    }

    public function createTransport(array $data): object
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

    public function flightByJourney(int $biayaId, int $pegawaiId, string $perjalanan): ?object
    {
        return $this->model->newQuery()
            ->where('biaya_id', $biayaId)
            ->where('pegawai_id', $pegawaiId)
            ->where('perjalanan', $perjalanan)
            ->where('jenis_transport', 'Pesawat')
            ->first();
    }
}
