<?php

namespace App\Repositories\Eloquent;

use App\Models\PejabatTtd;
use App\Repositories\Contracts\PejabatTtdRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
            ->leftJoin('files as f', 'f.id', '=', 'pejabat_ttd.template_file_id')
            ->orderBy('pejabat_ttd.created_at', 'DESC')
            ->select([
                'pejabat_ttd.id as id',
                'p.nip',
                'p.full_name',
                'pejabat_ttd.autorisasi',
                'pejabat_ttd.autorisasi_code',
                'pejabat_ttd.is_active',
                'pejabat_ttd.anggaran_id',
                'pejabat_ttd.template_file_id',
                'f.original_name as template_file_name',
                DB::raw("f.file_path || '/' || f.file_name as template_file_path"),
                'a.nama_rekening as anggaran_name',
                DB::raw("case when pejabat_ttd.autorisasi_code = 'BENDAHARA' then 'Bendahara ' || coalesce(a.nama_rekening,'')
                    when pejabat_ttd.autorisasi_code = 'PPTK' then 'PPTK ' || coalesce(a.nama_rekening,'')
                    else pejabat_ttd.autorisasi end as autorisasi_label"),
            ])
            ->get();
    }

    /**
     * Override find() bawaan supaya ikut bawa nama & path file template (join `files`)
     * — dipakai form Edit Master Pejabat untuk tampilkan link download template existing.
     */
    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()
            ->leftJoin('files as f', 'f.id', '=', 'pejabat_ttd.template_file_id')
            ->where('pejabat_ttd.id', $id)
            ->select([
                'pejabat_ttd.*',
                'f.original_name as template_file_name',
                DB::raw("f.file_path || '/' || f.file_name as template_file_path"),
            ])
            ->first();
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

    public function findByPegawaiAndAutorisasi(int $pegawaiId, string $autorisasiCode): ?object
    {
        return $this->model->newQuery()
            ->where('pegawai_id', $pegawaiId)
            ->where('autorisasi_code', $autorisasiCode)
            ->where('is_active', '1')
            ->first();
    }
}
