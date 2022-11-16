<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\{
    Pengeluaran,
    ReportSPPD,
    Biaya,
    Inap,
    Transport,
    SPT,
    SPTDetail,
    Pegawai,
    Anggaran
};

class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ReportSPPD::truncate();

        $sptAll = SPT::orderBy('no_index')->whereNotNull('settled_at')->whereNull('voided_at')->get();
        foreach($sptAll as $spt) {
            $sppd = SPTDetail::where('spt_id', $spt->id)->get();
            foreach($sppd as $dtl) {
                $userJbtn = Pegawai::where('pegawai.id', $dtl->pegawai_id)->select( 'full_name', 'jabatan')->first();

                $biaya = Biaya::where('spt_id', $spt->id)->where('pegawai_id', $dtl->pegawai_id)->first();
                
                $inap = Inap::where('biaya_id', $biaya->id)->where('pegawai_id', $dtl->pegawai_id)->first();

                $uangSaku = Pengeluaran::where('biaya_id', $biaya->id)
                ->where('pegawai_id', $dtl->pegawai_id)
                ->whereRaw("UPPER(kategori) like '%UANG SAKU%'")
                ->sum('total');

                $uangMakan = Pengeluaran::where('biaya_id', $biaya->id)
                ->where('pegawai_id', $dtl->pegawai_id)
                ->whereRaw("UPPER(kategori) like '%UANG MAKAN%'")
                ->sum('total');

                $uangRepresentasi = Pengeluaran::where('biaya_id', $biaya->id)
                ->where('pegawai_id', $dtl->pegawai_id)
                ->whereRaw("UPPER(kategori) like '%UANG REPRESENTASI%'")
                ->sum('total');

                $anggaran = Anggaran::where('id', $spt->anggaran_id)
                ->select('kode_rekening', 'nama_rekening')
                ->first();

                $uangDinasDalam = Pengeluaran::where('biaya_id', $biaya->id)
                ->where('pegawai_id', $dtl->pegawai_id)
                ->whereRaw("UPPER(kategori) like '%UANG PERJALANAN DINAS DALAM KOTA%'")
                ->sum('total');

                $uangLain = Pengeluaran::where('biaya_id', $biaya->id)
                ->where('pegawai_id', $dtl->pegawai_id)
                ->whereRaw("UPPER(kategori) not like '%UANG PERJALANAN DINAS DALAM KOTA%'")
                ->whereRaw("UPPER(kategori) not like '%UANG REPRESENTASI%'")
                ->whereRaw("UPPER(kategori) not like '%UANG SAKU%'")
                ->whereRaw("UPPER(kategori) not like '%UANG MAKAN%'")
                ->sum('total');

                $pesawatBrgkt = Transport::where('biaya_id', $biaya->id)
                ->where('pegawai_id', $dtl->pegawai_id)
                ->where('perjalanan', 'Berangkat')
                ->where('jenis_transport', 'Pesawat')
                ->first();

                $pesawatPlg = Transport::where('biaya_id', $biaya->id)
                ->where('pegawai_id', $dtl->pegawai_id)
                ->where('perjalanan', 'Pulang')
                ->where('jenis_transport', 'Pesawat')
                ->first();

                $asal = $spt->daerah_asal;
                $tujuan = $spt->daerah_tujuan;
                $checkin = $inap->tgl_checkin ?? null;
                $checkout = $inap->tgl_checkout ?? null;

                $pesbrgkt_tgl = $pesawatBrgkt->tgl ?? null;
                $peskmbl_tgl = $pesawatPlg->tgl ?? null;

                $report = ReportSPPD::insert([
                    'pegawai_id' => $dtl->pegawai_id,
                    'spt_id' => $spt->id,
                    'spt_detail_id' => $dtl->id,
                    'biaya_id' => $biaya->id,
                    'nama_rekening' => $anggaran->nama_rekening ?? null,
                    'kode_rekening' => $anggaran->kode_rekening ?? null,
                    'nama_pelaksana' => $userJbtn->full_name,
                    'jabatan' => $userJbtn->jabatan,
                    'no_pku' => null,
                    'no_spt' => $spt->no_spt,
                    'no_sppd' => null,
                    'kegiatan' => $spt->untuk,
                    'jml_hari' => $spt->jumlah_hari,
                    'penyelenggara' => 'SD Dalam Kab. Kerinci',
                    'lok_asal'=> $asal,
                    'lok_tujuan' => $tujuan,
                    'tgl_berangkat' => $spt->tgl_berangkat,
                    'tgl_kembali' => $spt->tgl_kembali,
                    'uang_saku' => $uangSaku ?? null,
                    'uang_makan' => $uangMakan ?? null,
                    'uang_representasi' => $uangRepresentasi ?? null,
                    'uang_lain' => $uangLain ?? null,
                    'uang_dinas_dlm' => $uangDinasDalam ?? null,
                    'uang_penginapan'  => $biaya->total_biaya_inap ?? null,
                    'uang_transport' => $biaya->total_biaya_transport ?? null,
                    'uang_total' => $biaya->total_biaya ?? null,
                    'uang_pesawat' => $biaya->total_biaya_pesawat ?? null,
                    'inap_hotel' => $inap->hotel ?? null,
                    'inap_room' => $inap->room ?? null,
                    'inap_checkin' => $checkin,
                    'inap_checkout' => $checkout,
                    'inap_jml_hari' => $inap->jml_hari ?? null,
                    'inap_per_malam' => $inap->harga ?? null,
                    'inap_jumlah' => $inap->total_bayar ?? null,
                    'pesbrgkt_maskapai' => $pesawatBrgkt->agen ?? null,
                    'pesbrgkt_no_tiket' => $pesawatBrgkt->no_tiket ?? null,
                    'pesbrgkt_kode_booking' => $pesawatBrgkt->kode_booking ?? null,
                    'pesbrgkt_no_penerbangan' => $pesawatBrgkt->no_penerbangan ?? null,
                    'pesbrgkt_tgl' => $pesbrgkt_tgl,
                    'pesbrgkt_jumlah' => $pesawatBrgkt->total_bayar ?? null,
                    'peskmbl_maskapai' => $pesawatPlg->agen ?? null,
                    'peskmbl_no_tiket' => $pesawatPlg->no_tiket ?? null,
                    'peskmbl_kode_booking' => $pesawatPlg->kode_booking ?? null,
                    'peskmbl_no_penerbangan' => $pesawatPlg->no_penerbangan ?? null,
                    'peskmbl_tgl' => $peskmbl_tgl,
                    'peskmbl_jumlah' => $pesawatPlg->total_bayar ?? null
                ]);
            }
        }
    }
}
