<?php

namespace App\Repositories\Eloquent;

use App\Models\Biaya;
use App\Repositories\Contracts\BiayaRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BiayaRepository extends BaseRepository implements BiayaRepositoryInterface
{
    public function __construct(Biaya $model)
    {
        parent::__construct($model);
    }

    public function findBySptAndPegawai(int $sptId, int $pegawaiId): ?object
    {
        return $this->model->newQuery()
            ->where('spt_id', $sptId)
            ->where('pegawai_id', $pegawaiId)
            ->first();
    }

    public function sumBySpt(int $sptId): float
    {
        return (float) $this->model->newQuery()->where('spt_id', $sptId)->sum('total_biaya');
    }

    public function deleteBySptId(int $sptId): void
    {
        $this->model->newQuery()->where('spt_id', $sptId)->delete();
    }

    public function createForSpt(int $sptId, int $anggaranId, int $pegawaiId): object
    {
        return $this->model->newQuery()->create([
            'spt_id' => $sptId,
            'anggaran_id' => $anggaranId,
            'pegawai_id' => $pegawaiId,
            'total_biaya_lainnya' => 0,
            'total_biaya_inap' => 0,
            'total_biaya_transport' => 0,
            'total_biaya' => 0,
        ]);
    }

    /**
     * Atomic recalculate of biaya totals from child tables.
     * Avoids the legacy manual-arithmetic race condition.
     */
    public function recalculate(int $biayaId): void
    {
        $other = DB::table('pengeluaran')->where('biaya_id', $biayaId)->whereNull('deleted_at')->sum('total');
        $transport = DB::table('transport')->where('biaya_id', $biayaId)->whereNull('deleted_at')->sum('total_bayar');
        $inap = DB::table('inap')->where('biaya_id', $biayaId)->whereNull('deleted_at')->sum('total_bayar');

        $this->model->newQuery()->where('id', $biayaId)->update([
            'total_biaya_lainnya' => $other,
            'total_biaya_transport' => $transport,
            'total_biaya_inap' => $inap,
            'total_biaya' => $other + $transport + $inap,
        ]);
    }

    /**
     * Replicates the legacy Postgres biaya_grid() union across the three
     * child tables. Uses parameterised queries (no string interpolation).
     */
    public function gridRows(int $biayaId, int $pegawaiId): array
    {
        $rows = [];

        $pengeluaran = DB::table('pengeluaran as p')
            ->leftJoin('files as f', 'f.id', '=', 'p.file_id')
            ->whereNull('p.deleted_at')
            ->where('p.biaya_id', $biayaId)
            ->where('p.pegawai_id', $pegawaiId)
            ->select(
                'p.id as reference_id',
                DB::raw("'PENGELUARAN' as modul"),
                DB::raw("'lainnya' as tipe"),
                'p.kategori as nama',
                'p.tgl as tanggal',
                DB::raw("p.jml || ' ' || p.satuan as keterangan"),
                DB::raw('p.total as biaya'),
                'p.catatan as remark',
                'p.created_at as created',
                DB::raw("f.file_path || '/' || f.file_name as file")
            )
            ->get();

        $inap = DB::table('inap as i')
            ->leftJoin('files as f', 'f.id', '=', 'i.file_id')
            ->whereNull('i.deleted_at')
            ->where('i.biaya_id', $biayaId)
            ->where('i.pegawai_id', $pegawaiId)
            ->select(
                'i.id as reference_id',
                DB::raw("'INAP' as modul"),
                DB::raw("'Penginapan' as tipe"),
                'i.hotel as nama',
                'i.tgl_checkin as tanggal',
                DB::raw("i.jml_hari || ' Hari' as keterangan"),
                DB::raw('i.total_bayar as biaya'),
                'i.catatan as remark',
                'i.created_at as created',
                DB::raw("f.file_path || '/' || f.file_name as file")
            )
            ->get();

        $transport = DB::table('transport as t')
            ->leftJoin('files as f', 'f.id', '=', 't.file_id')
            ->whereNull('t.deleted_at')
            ->where('t.biaya_id', $biayaId)
            ->where('t.pegawai_id', $pegawaiId)
            ->select(
                't.id as reference_id',
                DB::raw("'TRANSPORT' as modul"),
                't.jenis_transport as tipe',
                't.agen as nama',
                't.tgl as tanggal',
                't.perjalanan as keterangan',
                DB::raw('t.total_bayar as biaya'),
                't.catatan as remark',
                't.created_at as created',
                DB::raw("f.file_path || '/' || f.file_name as file")
            )
            ->get();

        $rows = $pengeluaran->concat($inap)->concat($transport)->all();

        usort($rows, function ($a, $b) {
            return strtotime($b->tanggal) - strtotime($a->tanggal);
        });

        return $rows;
    }

    public function findByAnggaranSum(int $anggaranId): float
    {
        return (float) $this->model->newQuery()
            ->where('anggaran_id', $anggaranId)
            ->sum('total_biaya');
    }

    public function getByAnggaranGrouped(): Builder
    {
        return $this->model->newQuery()
            ->whereNull('deleted_at')
            ->groupBy('anggaran_id')
            ->select('anggaran_id', DB::raw('sum(total_biaya) as total'));
    }
}
