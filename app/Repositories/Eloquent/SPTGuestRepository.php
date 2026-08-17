<?php

namespace App\Repositories\Eloquent;

use App\Models\SPTGuest;
use App\Repositories\Contracts\SPTGuestRepositoryInterface;

class SPTGuestRepository extends BaseRepository implements SPTGuestRepositoryInterface
{
    public function __construct(SPTGuest $model)
    {
        parent::__construct($model);
    }

    public function createGuest(int $sptId, string $key): void
    {
        $this->model->newQuery()->insert([
            'spt_id' => $sptId,
            'key' => $key,
        ]);
    }

    public function verify(string $key): ?object
    {
        return $this->model->newQuery()
            ->join('spt', 'spt.id', '=', 'spt_guest.spt_id')
            ->where('spt_guest.key', $key)
            ->select(
                'spt.id',
                'spt.tgl_spt',
                'spt.jenis_dinas',
                'spt.no_spt',
                'spt.daerah_asal',
                'spt.daerah_tujuan',
                'spt.tgl_berangkat',
                'spt.tgl_kembali',
                'spt.transportasi',
                'spt.status'
            )
            ->first();
    }
}
