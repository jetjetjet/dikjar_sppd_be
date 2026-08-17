<?php

namespace App\Repositories\Eloquent;

use App\Models\PejabatTtd;
use App\Repositories\Contracts\PejabatTtdRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PejabatTtdRepository extends BaseRepository implements PejabatTtdRepositoryInterface
{
    public function __construct(PejabatTtd $model)
    {
        parent::__construct($model);
    }

    public function grid(): Collection
    {
        return $this->model->newQuery()
            ->join('pegawai as p', 'p.id', '=', 'pejabat_ttd.pegawai_id')
            ->leftJoin('anggaran as a', 'a.id', '=', 'pejabat_ttd.anggaran_id')
            ->orderBy('pejabat_ttd.created_at', 'DESC')
            ->select([
                'pejabat_ttd.id as id',
                'p.nip',
                'p.full_name',
                'pejabat_ttd.autorisasi',
                'pejabat_ttd.autorisasi_code',
                'pejabat_ttd.is_active',
                'pejabat_ttd.anggaran_id',
                'a.nama_rekening as anggaran_name',
                DB::raw("case when pejabat_ttd.autorisasi_code = 'BENDAHARA' then 'Bendahara ' || coalesce(a.nama_rekening,'')
                    when pejabat_ttd.autorisasi_code = 'PPTK' then 'PPTK ' || coalesce(a.nama_rekening,'')
                    else pejabat_ttd.autorisasi end as autorisasi_label"),
            ])
            ->get();
    }

    public function search(?string $filter, ?int $anggaranId): Collection
    {
        $q = $this->model->newQuery()
            ->join('pegawai as p', 'p.id', '=', 'pejabat_ttd.pegawai_id')
            ->where('is_active', '1')
            ->select('p.id as code', 'p.full_name as label');

        if ($filter !== null) {
            $q->where('autorisasi_code', $filter);
        }

        if ($anggaranId !== null) {
            $q->where('anggaran_id', $anggaranId);
        }

        return $q->get();
    }

    public function existsDuplicate(int $pegawaiId, string $autorisasi): bool
    {
        return $this->model->newQuery()
            ->where('pegawai_id', $pegawaiId)
            ->where('autorisasi', $autorisasi)
            ->where('is_active', '1')
            ->exists();
    }
}
