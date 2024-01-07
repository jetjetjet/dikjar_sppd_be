<?php

namespace App\Services;

use App\Repositories\AnggaranRepository;

class AnggaranService
{
    /**
     * 
     */
    public function __construct(protected AnggaranRepository $repository)
    {
        //
    }
}
