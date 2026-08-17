<?php

namespace App\Services;

use Carbon\Carbon;

class DocumentMapperService
{
    /**
     * Map an SPT row into document template values (shared across SPT/SPPD flows).
     */
    public function mapSPT(mixed $db): object
    {
        $sp = new \stdClass;

        $sp->id = $db->id ?? '';
        $sp->jml_hari = $db->jumlah_hari ?? '';
        $sp->tgl_berangkat = isset($db->tgl_berangkat) ? (new Carbon($db->tgl_berangkat))->isoFormat('D MMMM Y') : '';
        $sp->tgl_kembali = isset($db->tgl_kembali) ? (new Carbon($db->tgl_kembali))->isoFormat('D MMMM Y') : '';
        $sp->tgl_spt = isset($db->tgl_spt) ? (new Carbon($db->tgl_spt))->isoFormat('D MMMM Y') : '';
        $sp->daerah_asal = $db->daerah_asal ?? '';
        $sp->daerah_tujuan = $db->daerah_tujuan ?? '';
        $sp->no_spt = $db->no_spt ?? '';
        $sp->periode = $db->periode ?? '';
        $sp->no_index = $db->no_index ?? '';
        $sp->untuk = $db->untuk ?? '';
        $sp->transportasi = $db->transportasi ?? '';
        $sp->dasar_pelaksana = $db->dasar_pelaksana ?? '';

        return $sp;
    }

    /**
     * Map a ledger row (pengeluaran/transport/inap) into rumming template values.
     */
    public function mapBiaya(mixed $db): object
    {
        $bs = new \stdClass;

        $bs->pengeluaran = $db->pengeluaran ?? '';
        $bs->j = $db->qty ?? '';
        $bs->biaya = isset($db->harga) ? number_format($db->harga) : '';
        $bs->total = isset($db->harga) && isset($db->qty) ? number_format($db->harga * $db->qty) : '';
        $bs->totalRaw = isset($db->harga) && isset($db->qty) ? $db->harga * $db->qty : 0;
        $bs->cttn = $db->catatan ?? '';

        return $bs;
    }
}
