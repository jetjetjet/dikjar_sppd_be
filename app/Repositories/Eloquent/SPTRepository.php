<?php

namespace App\Repositories\Eloquent;

use App\Models\SPT;
use App\Models\SPTDetail;
use App\Repositories\Contracts\SPTRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SPTRepository extends BaseRepository implements SPTRepositoryInterface
{
    public function __construct(SPT $model)
    {
        parent::__construct($model);
    }

    /**
     * Build the grid query (tahun filter, admin/creator filter).
     * Uses plain columns/joins running on both PostgreSQL and SQLite; all
     * presentation fields (tgl, name, status label, badge, keterangan,
     * can_generate) are computed in the service layer instead of raw SQL
     * string interpolation of user input.
     */
    public function gridIndex(?string $tahun, ?int $loginId, bool $isAdmin): Builder
    {
        $q = SPT::query()
            ->with('details.pegawai')
            ->select([
                'id',
                'no_spt',
                'jenis_dinas',
                'tgl_berangkat',
                'tgl_kembali',
                'daerah_tujuan',
                'untuk',
                'status',
                'settled_at',
                'proceed_at',
                'finished_at',
                'spt_file_id',
            ]);

        if ($tahun !== null) {
            $q->where('periode', (int) $tahun);
        }

        if (! $isAdmin && $loginId !== null) {
            $q->where('created_by', $loginId);
        }

        $q->orderByDesc('periode')->orderByDesc('no_index');

        return $q;
    }

    public function getGrid(?string $tahun, ?int $loginId, bool $isAdmin): Collection
    {
        return $this->gridIndex($tahun, $loginId, $isAdmin)->get();
    }

    public function findWithDetail(int $id): mixed
    {
        $data = DB::table('spt')
            ->join('anggaran as ag', 'ag.id', '=', 'spt.anggaran_id')
            ->join('pegawai as bdh', 'bdh.id', '=', 'spt.bendahara_id')
            ->join('pegawai as pgn', 'pgn.id', '=', 'spt.pengguna_anggaran_id')
            ->join('pegawai as pptk', 'pptk.id', '=', 'spt.pptk_id')
            ->join('pegawai as pttd', 'pttd.id', '=', 'spt.pttd_id')
            ->join('pegawai as pel', 'pel.id', '=', 'spt.pelaksana_id')
            ->where('spt.id', $id)
            ->select([
                'spt.*',
                'ag.kode_rekening as anggaran_text',
                'ag.nama_rekening as anggaran_name',
                'pgn.full_name as pengguna_anggaran_text',
                'bdh.full_name as bendahara_text',
                'pptk.full_name as pptk_text',
                'pttd.full_name as pttd_text',
                'pel.full_name as pelaksana_text',
            ])
            ->first();

        if ($data) {
            $data->pegawai_id = SPTDetail::where('spt_id', $id)
                ->where('is_pelaksana', '0')
                ->pluck('pegawai_id');
        }

        return $data;
    }

    public function getByPeriode(string $tahun): int
    {
        return (int) SPT::whereNull('deleted_at')->where('periode', $tahun)->max('no_index');
    }

    public function getFile(int $id): ?object
    {
        return DB::table('files')->where('id', $id)->first();
    }

    public function findWithFile(int $id): ?object
    {
        return DB::table('spt')
            ->join('files as f', 'f.id', '=', 'spt.spt_file_id')
            ->where('spt.id', $id)
            ->select('f.file_path', 'f.file_name')
            ->first();
    }

    public function allocateNoIndex(string $tahun): int
    {
        // PostgreSQL tidak mengizinkan FOR UPDATE dikombinasikan dengan aggregate
        // function (MAX) dalam satu query. Kunci baris no_index tertinggi saja
        // (ORDER BY ... LIMIT 1 FOR UPDATE) — cukup untuk mencegah dua transaksi
        // konkuren mengalokasikan nomor SPT yang sama pada periode yang sama.
        $max = SPT::whereNull('deleted_at')
            ->where('periode', $tahun)
            ->orderByDesc('no_index')
            ->lockForUpdate()
            ->value('no_index');

        return (int) $max + 1;
    }

    public function countActiveByAnggaranId(int $anggaranId): int
    {
        return SPT::whereNull('deleted_at')
            ->where('anggaran_id', $anggaranId)
            ->whereNotNull('settled_at')
            ->count();
    }

    public function getByAnggaranRealisasi(): Builder
    {
        return DB::table('spt as s')
            ->join('biaya as b', 'b.spt_id', '=', 's.id')
            ->whereNull('b.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereNull('s.voided_at')
            ->whereNotNull('s.settled_at')
            ->groupBy('s.anggaran_id')
            ->select('s.anggaran_id', DB::raw('sum(b.total_biaya) as realisasi'));
    }
}
