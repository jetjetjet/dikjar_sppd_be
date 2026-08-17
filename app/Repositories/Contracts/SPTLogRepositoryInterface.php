<?php

namespace App\Repositories\Contracts;

interface SPTLogRepositoryInterface extends BaseRepositoryInterface
{
    public function log(?int $userId, ?string $username, int $referenceId, string $aksi, bool $success = true, ?string $detail = null): void;
}
