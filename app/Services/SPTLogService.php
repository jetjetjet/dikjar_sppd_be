<?php

namespace App\Services;

use App\Models\SPTLog;
use Illuminate\Support\Facades\DB;

class SPTLogService
{
    public function grid(array $filters)
    {
        $q = SPTLog::join('spt', 'spt.id', '=', 'spt_log.reference_id')
            ->select(
                'spt.no_spt',
                'spt_log.username',
                'spt_log.reference_id',
                'spt_log.aksi',
                'spt_log.success',
                'spt_log.detail',
                DB::raw('spt_log.created_at as tanggal')
            );

        $page = (int) ($filters['page'] ?? 1);
        $perPage = (int) ($filters['perPage'] ?? $filters['pageLimit'] ?? 20);

        // simple search across aksi/username
        if (! empty($filters['q'])) {
            $q->where(function ($query) use ($filters) {
                $query->where('spt_log.aksi', 'like', '%'.$filters['q'].'%')
                    ->orWhere('spt_log.username', 'like', '%'.$filters['q'].'%')
                    ->orWhere('spt.no_spt', 'like', '%'.$filters['q'].'%');
            });
        }

        $q->orderByDesc('spt_log.created_at');

        return $q->paginate($perPage, ['*'], 'page', $page);
    }
}
