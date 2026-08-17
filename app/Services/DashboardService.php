<?php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\SPT;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Realisasi subquery shared by anggaran dashboard.
     */
    protected function realisasiSub()
    {
        return DB::table('spt as s')
            ->join('biaya as b', 'b.spt_id', '=', 's.id')
            ->whereNull('b.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereNotNull('s.settled_at')
            ->groupBy('s.anggaran_id')
            ->select('s.anggaran_id', DB::raw('sum(b.total_biaya) as realisasi'));
    }

    public function anggaran()
    {
        $role = auth('sanctum')->user()->roles->pluck('name')[0] ?? null;
        $isAdmin = auth('sanctum')->user()->tokenCan('is_admin');

        $q = Anggaran::query()
            ->leftJoinSub($this->realisasiSub(), 'r', function ($join) {
                $join->on('anggaran.id', '=', 'r.anggaran_id');
            })
            ->where('periode', date('Y'));

        if (! $isAdmin && $role !== null) {
            $q->where('bidang', $role);
        }

        $anggaran = $q->select(
            'anggaran.id',
            'kode_rekening',
            'nama_rekening',
            DB::raw('coalesce(realisasi, 0) as realisasi'),
            'pagu',
            'periode'
        )->get();

        $temp = ['label' => [], 'anggaran' => [], 'realisasi' => []];

        foreach ($anggaran as $ang) {
            $temp['label'][] = $ang->nama_rekening;
            $temp['anggaran'][] = (float) $ang->pagu;
            $temp['realisasi'][] = (float) $ang->realisasi;
        }

        return $temp;
    }

    public function pegawaiDinas()
    {
        $role = auth('sanctum')->user()->roles->pluck('name')[0] ?? null;
        $isAdmin = auth('sanctum')->user()->tokenCan('is_admin');

        $q = SPT::join('spt_detail as sd', function ($query) {
            $query->on('sd.spt_id', '=', 'spt.id')->whereNull('sd.deleted_at');
        })
            ->join('pegawai as p', 'p.id', '=', 'sd.pegawai_id')
            ->whereNotNull('spt.proceed_at')
            ->whereNull('spt.voided_at')
            ->whereNull('spt.settled_at')
            ->whereNull('sd.settled_at')
            ->select(
                'spt.no_spt',
                'spt.id as spt_id',
                'spt.anggaran_id',
                'spt.status',
                'spt.tgl_berangkat',
                'spt.tgl_kembali',
                'spt.finished_at',
                'p.full_name',
                'p.jabatan',
                'spt.daerah_tujuan',
                'p.path_foto'
            );

        if (! $isAdmin && $role !== null) {
            $q->where('spt.bidang', $role);
        }

        $rows = $q->orderBy('no_spt', 'DESC')->get();

        return $rows->map(function ($row) {
            $row->status = ucwords(strtolower($row->status));
            $row->badge = $this->statusBadge($row->status);
            $row->keterangan = $this->lateKeterangan($row);
            $row->path_foto = $row->path_foto ?: '/storage/profile/user.png';
            $row->tgl_berangkat = Carbon::parse($row->tgl_berangkat)->format('d/m/Y');
            $row->tgl_kembali = Carbon::parse($row->tgl_kembali)->format('d/m/Y');

            return $row;
        })->values();
    }

    protected function statusBadge(string $status): string
    {
        return match ($status) {
            'Konsep' => 'badge badge-secondary',
            'Proses' => 'badge badge-primary',
            'Kembali' => 'badge badge-info',
            'Kwitansi' => 'badge badge-warning',
            'Selesai' => 'badge badge-success',
            default => 'badge badge-dark',
        };
    }

    protected function lateKeterangan($row): string
    {
        if ($row->proceed_at !== null && $row->finished_at === null) {
            $days = Carbon::parse($row->tgl_kembali)->diffInDays(Carbon::now(), false);
            if ($days < 0) {
                return 'Telat '.abs($days).' hari';
            }
        }

        return '';
    }
}
