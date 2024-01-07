<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Repositories\BaseRepository;

class PegawaiRepository extends BaseRepository
{
    /**
     * 
     */
    public function __construct(Pegawai $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        return $this->model
            ->orderBy('id')
            ->get();
    }
}
