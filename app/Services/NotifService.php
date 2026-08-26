<?php

namespace App\Services;

use App\Models\SPT;
use Carbon\Carbon;

class NotifService
{
    public function notif()
    {
        $q = $this->baseQuery();

        $data = $q->limit(5)
            ->select('id', 'no_spt', 'tgl_kembali', 'proceed_at', 'finished_at')
            ->get()
            ->map(fn ($r) => (object) [
                'id' => $r->id,
                'no_spt' => $r->no_spt,
                'telat' => $this->telatLabel($r),
            ]);

        $count = $q->count();

        return [
            'isNotif' => $count > 0,
            'data' => $data,
        ];
    }

    public function listNotif()
    {
        $q = $this->baseQuery();

        return $q->select(
            'no_spt',
            'id',
            'daerah_tujuan',
            'status',
            'tgl_berangkat',
            'tgl_kembali',
            'proceed_at',
            'finished_at'
        )
            ->orderByDesc('tgl_kembali')
            ->get()
            ->map(function ($r) {
                $r->status = ucwords(strtolower($r->status));
                $r->badge = match ($r->status) {
                    'Proses' => 'badge badge-primary',
                    'Kembali' => 'badge badge-info',
                    'Kwitansi' => 'badge badge-warning',
                    default => 'badge badge-dark',
                };
                $r->keterangan = $this->telatLabel($r);
                $r->tgl_berangkat = Carbon::parse($r->tgl_berangkat)->format('d/m/Y');
                $r->tgl_kembali = Carbon::parse($r->tgl_kembali)->format('d/m/Y');

                return $r;
            })
            ->values();
    }

    protected function baseQuery()
    {
        $user = auth('sanctum')->user();
        $isAdmin = $user->tokenCan('is_admin');

        $q = SPT::query()
            ->whereNull('finished_at')
            ->whereNotNull('proceed_at')
            ->where('status', '!=', 'VOID');

        if (! $isAdmin) {
            $q->where('spt.created_by', $user->id);
        }

        return $q;
    }

    protected function telatLabel($r): string
    {
        // Carbon 3 default-nya diffInDays() balikin float (mis. 2.6543), bukan
        // int seperti Carbon 2 — dibulatkan (int cast) supaya labelnya bulat,
        // tanpa desimal.
        // diffInDays($other, false): POSITIF kalau tgl_kembali sudah lewat dari $other
        // (now), NEGATIF kalau tgl_kembali masih di masa depan — jadi "telat" itu > 0.
        $days = (int) Carbon::parse($r->tgl_kembali)->diffInDays(Carbon::now(), false);

        return $days > 0 ? 'Telat '.abs($days).' hari' : '';
    }
}
