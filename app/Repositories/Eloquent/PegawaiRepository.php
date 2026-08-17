<?php

namespace App\Repositories\Eloquent;

use App\Models\Pegawai;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PegawaiRepository extends BaseRepository implements PegawaiRepositoryInterface
{
    public function __construct(Pegawai $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return $this->model->newQuery()->orderBy('id')->get();
    }

    public function activeQuery(): Builder
    {
        return $this->model->newQuery()->aktif();
    }

    /**
     * Search pegawai by filter type used across the app:
     * 'all', 'spt', 'spt_edit', 'user', 'report'.
     */
    public function search(array $filters, ?int $editId = null): Collection
    {
        $filter = $filters['filter'] ?? 'all';

        $query = $this->activeQuery();

        if ($filter === 'user') {
            $query->select('pegawai.email as code', 'full_name as label')
                ->whereRaw('pegawai.email not in (select users.email from users)')
                ->where('pegawai_app', '1');
        } else {
            $query->select('pegawai.id as code', 'full_name as label');

            if ($filter === 'spt' || $filter === 'spt_edit') {
                $activeStatus = ['KONSEP', 'PROSES', 'KWITANSI', 'KEMBALI'];

                $excluded = DB::table('spt_detail as sd')
                    ->join('spt', 'spt.id', '=', 'sd.spt_id')
                    ->whereIn('spt.status', $activeStatus)
                    ->whereNull('spt.deleted_at')
                    ->whereNull('sd.deleted_at');

                if ($filter === 'spt_edit' && $editId !== null) {
                    $excluded->where('spt_id', '!=', $editId);
                }

                $query->whereNotIn('pegawai.id', $excluded->pluck('sd.pegawai_id'))
                    ->where('pegawai_app', '1');
            }

            if ($filter === 'report') {
                $query->where('pegawai_app', '1');
            }
        }

        return $query->get();
    }
}
