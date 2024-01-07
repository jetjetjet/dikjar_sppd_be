<?php

namespace App\Services;

use App\Repositories\PegawaiRepository;

class PegawaiService
{
    /**
     * 
     */
    public function __construct(protected PegawaiRepository $repository)
    {
        //
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function store(array $payload)
    {
        
    }
}
