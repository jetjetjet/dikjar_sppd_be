<?php

namespace App\Repositories\Eloquent;

use App\Models\SPTLog;
use App\Repositories\Contracts\SPTLogRepositoryInterface;

class SPTLogRepository extends BaseRepository implements SPTLogRepositoryInterface
{
    public function __construct(SPTLog $model)
    {
        parent::__construct($model);
    }

    public function log(?int $userId, ?string $username, int $referenceId, string $aksi, bool $success = true, ?string $detail = null): void
    {
        $this->model->newQuery()->create([
            'user_id' => $userId,
            'username' => $username,
            'reference_id' => $referenceId,
            'aksi' => $aksi,
            'success' => $success ? '1' : '0',
            'detail' => $detail,
        ]);
    }
}
