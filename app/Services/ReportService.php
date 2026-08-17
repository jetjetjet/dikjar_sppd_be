<?php

namespace App\Services;

use App\Models\SPT;
use App\Repositories\Contracts\ReportSPPDRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(protected ReportSPPDRepositoryInterface $reportRepository) {}

    public function reportByFinishedSPT(string $jenisDinas, int $tahunLaporan)
    {
        $data = $this->reportRepository->getByPeriode($tahunLaporan);

        if ($jenisDinas === 'Dalam Daerah') {
            $data = $data->filter(fn ($row) => $row->lok_asal !== 'Kabupaten Kerinci');
        } elseif ($jenisDinas === 'Luar Daerah') {
            $data = $data->filter(fn ($row) => $row->lok_asal === 'Kabupaten Kerinci');
        }

        return $data->values();
    }

    public function reportByPegawai(array $inputs)
    {
        $q = SPT::join('spt_detail as sd', function ($on) {
            $on->on('sd.spt_id', '=', 'spt.id')->whereNull('sd.deleted_at');
        })
            ->join('pegawai as p', 'p.id', '=', 'sd.pegawai_id')
            ->join('biaya as b', function ($on) use ($inputs) {
                $on->on('b.spt_id', '=', 'spt.id')
                    ->where('b.pegawai_id', (int) ($inputs['pegawai_id'] ?? 0))
                    ->whereNull('b.deleted_at');
            })
            ->where('p.id', (int) ($inputs['pegawai_id'] ?? 0));

        if (! empty($inputs['tgl_berangkat']) && ! empty($inputs['tgl_kembali'])) {
            $q->whereBetween('spt.tgl_berangkat', [$inputs['tgl_berangkat'], $inputs['tgl_kembali']]);
        }

        if (isset($inputs['status'])) {
            $q = $this->applyStatusFilter($q, $inputs['status']);
        }

        $rows = $q->select(
            'spt.id',
            'p.full_name',
            'spt.no_spt',
            'spt.daerah_asal as asal',
            'spt.daerah_tujuan as tujuan',
            'spt.tgl_berangkat',
            'spt.tgl_kembali',
            'spt.completed_at',
            'spt.settled_at',
            'spt.status',
            DB::raw('b.total_biaya as jml_biaya')
        )->get();

        return $rows->map(function ($row) {
            $row->jml_biaya = number_format((float) $row->jml_biaya);
            $row->status = $this->statusLabel($row);

            return $row;
        });
    }

    protected function applyStatusFilter($q, string $status)
    {
        return match ($status) {
            'KONSEP' => $q->where('spt.status', 'KONSEP'),
            'PROSES' => $q->whereNotIn('spt.status', ['KONSEP'])->whereNull('spt.settled_at'),
            'SELESAI' => $q->whereNotNull('spt.settled_at'),
            default => $q,
        };
    }

    protected function statusLabel($row): string
    {
        if ($row->status === 'KONSEP') {
            return 'Draf';
        }
        if ($row->completed_at === null) {
            return 'Proses';
        }
        if ($row->settled_at === null) {
            return 'Kembali';
        }

        return 'Selesai';
    }
}
