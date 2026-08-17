<?php

namespace App\Repositories\Contracts;

interface SPTGuestRepositoryInterface extends BaseRepositoryInterface
{
    public function createGuest(int $sptId, string $key): void;

    public function verify(string $key): ?object;
}
