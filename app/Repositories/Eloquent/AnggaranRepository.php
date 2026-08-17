<?php

namespace App\Repositories\Eloquent;

use App\Models\Anggaran;
use App\Repositories\Contracts\AnggaranRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AnggaranRepository extends BaseRepository implements AnggaranRepositoryInterface
{
    public function __construct(Anggaran $model)
    {
        parent::__construct($model);
    }

    /**
     * Re-usable "realisasi" subquery shared by grid/search/dashboard.
     */
    protected function realisasiSub(): \Illuminate\Database\Query\Builder
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

    public function getGridQuery(bool $isAdmin, ?string $role): Builder
    {
        $q = Anggaran::query()
            ->leftJoinSub($this->realisasiSub(), 'r', function ($join) {
                $join->on('anggaran.id', '=', 'r.anggaran_id');
            })
            ->orderByDesc('periode')
            ->orderBy('kode_rekening')
            ->orderBy('nama_rekening');

        if (! $isAdmin && $role !== null) {
            $q->where('bidang', $role);
        }

        return $q;
    }

    public function grid(int $roleId, bool $isAdmin, ?string $role): Collection
    {
        return $this->getGridQuery($isAdmin, $role)->get();
    }

    public function getSearchQuery(bool $isAdmin, ?string $role): Builder
    {
        return $this->model->newQuery()
            ->leftJoinSub($this->realisasiSub(), 'r', function ($join) {
                $join->on('anggaran.id', '=', 'r.anggaran_id');
            })
            ->join('pegawai as bdh', 'bdh.id', '=', 'anggaran.bendahara_id')
            ->join('pegawai as pgn', 'pgn.id', '=', 'anggaran.pengguna_id')
            ->join('pegawai as pptk', 'pptk.id', '=', 'anggaran.pptk_id')
            ->where('periode', date('Y'))
            ->select(
                'anggaran.id',
                'anggaran.kode_rekening',
                'anggaran.nama_rekening',
                'anggaran.uraian',
                'anggaran.pagu',
                'anggaran.bendahara_id',
                'anggaran.pptk_id',
                'anggaran.pengguna_id',
                'bdh.full_name as bendahara_name',
                'pptk.full_name as pptk_name',
                'pgn.full_name as pengguna_name',
                DB::raw('coalesce(r.realisasi, 0) as realisasi')
            );
    }

    public function search(int $roleId, bool $isAdmin, ?string $role): Collection
    {
        $q = $this->getSearchQuery($isAdmin, $role);

        if (! $isAdmin && $role !== null) {
            $q->where('anggaran.bidang', $role);
        }

        return $q->get();
    }
}
