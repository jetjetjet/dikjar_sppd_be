<?php

namespace App\Repositories;

use App\Models\Anggaran;
use App\Repositories\BaseRepository;

class StockRepository extends BaseRepository
{
    /**
     * 
     */
    public function __construct(Anggaran $model)
    {
        parent::__construct($model);
    }
}
