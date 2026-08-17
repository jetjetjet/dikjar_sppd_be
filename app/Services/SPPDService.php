<?php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\Biaya;
use App\Models\Inap;
use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\Transport;
use App\Repositories\Contracts\BiayaRepositoryInterface;
use App\Repositories\Contracts\FileRepositoryInterface;
use App\Repositories\Contracts\InapRepositoryInterface;
use App\Repositories\Contracts\PengeluaranRepositoryInterface;
use App\Repositories\Contracts\ReportSPPDRepositoryInterface;
use App\Repositories\Contracts\SPTDetailRepositoryInterface;
use App\Repositories\Contracts\SPTLogRepositoryInterface;
use App\Repositories\Contracts\SPTRepositoryInterface;
use App\Repositories\Contracts\TransportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SPPDService
{
    public function __construct(
        protected SPTRepositoryInterface $sptRepository,
        protected SPTDetailRepositoryInterface $sptDetailRepository,
        protected BiayaRepositoryInterface $biayaRepository,
        protected TransportRepositoryInterface $transportRepository,
        protected InapRepositoryInterface $inapRepository,
        protected PengeluaranRepositoryInterface $pengeluaranRepository,
        protected ReportSPPDRepositoryInterface $reportRepository,
        protected FileRepositoryInterface $fileRepository,
        protected SPTLogRepositoryInterface $sptLogRepository,
        protected DocumentGeneratorService $documentGenerator,
    ) {}

    public function getGrid(int $sptId)
    {
        $user = auth('sanctum')->user();
        $isAdmin = $user->tokenCan('is_admin') ? 1 : 0;
        $loginId = $user->id;

        $biaya = DB::table('biaya')->whereNull('deleted_at')
            ->select('pegawai_id', 'spt_id', 'total_biaya');

        $details = SPTDetail::join('pegawai as p', 'p.id', '=', 'spt_detail.pegawai_id')
            ->leftJoinSub($biaya, 'biaya', function ($join) {
                $join->on('spt_detail.spt_id', '=', 'biaya.spt_id')
                    ->on('spt_detail.pegawai_id', '=', 'biaya.pegawai_id');
            })
            ->where('spt_detail.spt_id', $sptId)
            ->select([
                'spt_detail.id',
                'spt_detail.sppd_file_id',
                'spt_detail.created_by',
                'p.id as pegawai_id',
                'full_name',
                'nip',
                DB::raw('coalesce(total_biaya,0) as total_biaya'),
            ])
            ->orderBy('is_pelaksana', 'DESC')
            ->orderBy('nip')
            ->orderBy('full_name')
            ->get();

        $total = 0;
        foreach ($details as $detail) {
            $total += $detail->total_biaya;
            // Compute permission flags in PHP (no SQL interpolation needed).
            $detail->can_edit = ($isAdmin === 1 || (int) $detail->created_by === $loginId);
            unset($detail->created_by);
        }

        return [[
            'children' => $details,
            'total_biaya' => $total,
            'nip' => 'Total Keseluruhan',
        ]];
    }

    public function getDetail(int $sptId, int $sptDetailId, int $pegawaiId)
    {
        $user = auth('sanctum')->user();
        $isAdmin = $user->tokenCan('is_admin') ? 1 : 0;
        $loginId = $user->id;

        // Driver-agnostic join; computed presentation fields resolved in PHP.
        $spt = SPT::join('spt_detail as sd', 'sd.spt_id', '=', 'spt.id')
            ->join('pegawai as b', 'b.id', '=', 'spt.bendahara_id')
            ->join('pegawai as pptk', 'pptk.id', '=', 'spt.pptk_id')
            ->join('pegawai as pttd', 'pttd.id', '=', 'spt.pttd_id')
            ->join('pegawai as pel', 'pel.id', '=', 'spt.pelaksana_id')
            ->join('anggaran as ang', 'ang.id', '=', 'spt.anggaran_id')
            ->where('sd.id', $sptDetailId)
            ->select([
                'spt.anggaran_id',
                'spt.no_spt',
                'spt.tgl_berangkat',
                'spt.tgl_kembali',
                'spt.daerah_asal',
                'spt.daerah_tujuan',
                'spt.transportasi',
                'spt.settled_at',
                'spt.dasar_pelaksana',
                'spt.untuk',
                'spt.created_by',
                'b.full_name as bendahara_name',
                'pptk.full_name as pptk_name',
                'pttd.full_name as pttd_name',
                'pel.full_name as pelaksana_name',
                'ang.nama_rekening as anggaran_name',
                'spt.voided_at',
                'spt.void_remark',
                'spt.status',
            ])
            ->first();

        if ($spt === null) {
            return null;
        }

        $spt->tglb_text = Carbon::parse($spt->tgl_berangkat)->format('d-m-Y');
        $spt->tglk_text = Carbon::parse($spt->tgl_kembali)->format('d-m-Y');

        $pegawai = DB::table('pegawai')->where('id', $pegawaiId)->whereNull('deleted_at')->value('full_name');
        $spt->pegawai_text = $pegawai;

        $biaya = $this->biayaRepository->findBySptAndPegawai($sptId, $pegawaiId);
        $spt->biaya_id = $biaya->id ?? null;
        $spt->total_biaya = $biaya->total_biaya ?? 0;

        $spt->sppd_file_id = SPTDetail::where('spt_id', $sptId)
            ->where('pegawai_id', $pegawaiId)
            ->value('sppd_file_id');

        $spt->can_kwitansi = ((int) $spt->created_by === $loginId || $isAdmin === 1) ? 1 : 0;
        unset($spt->created_by);

        $biayaSum = $this->biayaRepository->findByAnggaranSum((int) $spt->anggaran_id);
        $anggaran = Anggaran::where('id', $spt->anggaran_id)->first();
        if ($anggaran) {
            $spt->anggaran_ready = 'Rp '.number_format($anggaran->pagu - $biayaSum);
        }

        return $spt;
    }

    public function getSPPDFile(int $sptDetailId): ?string
    {
        $sppd = $this->sptDetailRepository->find($sptDetailId);
        if ($sppd === null || $sppd->sppd_file_id === null) {
            return null;
        }

        $file = $this->fileRepository->getById((int) $sppd->sppd_file_id);

        return $file ? ($file->file_path.'/'.$file->file_name) : null;
    }

    public function getSPPDDocument(int $sptDetailId, int $pegawaiId): ?string
    {
        $data = SPTDetail::join('files as f', 'f.id', '=', 'spt_detail.sppd_file_id')
            ->where('spt_detail.id', $sptDetailId)
            ->where('spt_detail.pegawai_id', $pegawaiId)
            ->select('f.file_path', 'f.file_name')
            ->first();

        return $data ? ($data->file_path.'/'.$data->file_name) : null;
    }

    public function cetakRumming(int $sptId, int $biayaId, int $pegawaiId): string
    {
        $sptDetail = $this->sptDetailRepository->getFirstBySptAndPegawai($sptId, $pegawaiId);

        if ($sptDetail === null) {
            throw new \Exception('Data tidak ditemukan.');
        }

        if ($sptDetail->rumming_file_id !== null) {
            $file = $this->fileRepository->getById((int) $sptDetail->rumming_file_id);

            return $file->file_path.'/'.$file->file_name;
        }

        $filePath = $this->documentGenerator->generateRumming($sptId, $biayaId, $pegawaiId);

        $pegawai = Pegawai::where('id', $pegawaiId)->select(DB::raw("coalesce(nip,'-') as nip"), 'full_name as pegawai_name')->first();

        $this->sptDetailRepository->updateById((int) $sptDetail->id, [
            'rumming_file_id' => $this->fileIdFromPath($filePath),
        ]);

        $this->sptLogRepository->log(
            auth('sanctum')->id(),
            auth('sanctum')->user()->pegawai->full_name ?? null,
            $sptId,
            'Cetak Rumming '.($pegawai->pegawai_name ?? '')
        );

        return $filePath;
    }

    public function cetakKwitansi(int $sptId): string
    {
        $spt = $this->sptRepository->findOrFail($sptId);

        if ($spt->kwitansi_file_id !== null) {
            $file = $this->fileRepository->getById((int) $spt->kwitansi_file_id);

            return $file->file_path.'/'.$file->file_name;
        }

        $filePath = DB::transaction(function () use ($sptId, $spt) {
            $filePath = $this->documentGenerator->generateKwitansi($sptId);

            $loginId = auth('sanctum')->id();
            $this->sptRepository->updateById($sptId, [
                'settled_at' => DB::raw('now()'),
                'settled_by' => $loginId,
                'kwitansi_file_id' => $this->fileIdFromPath($filePath),
                'status' => 'KWITANSI',
            ]);

            $this->sptDetailRepository->updateSettledBySptId($sptId, (string) $loginId);

            $this->saveReport($sptId, $spt);

            $this->sptLogRepository->log(
                $loginId,
                auth('sanctum')->user()->pegawai->full_name ?? null,
                $sptId,
                'Cetak Kwitansi'
            );

            return $filePath;
        });

        return $filePath;
    }

    public function cetakLaporan(int $sptId, ?string $hasil, ?string $saran): string
    {
        $spt = $this->sptRepository->findOrFail($sptId);

        $updateData = ['hasil' => $hasil ?? '', 'saran' => $saran ?? ''];
        if ($spt->finished_at === null) {
            $updateData = array_merge($updateData, [
                'finished_at' => now()->toDateTimeString(),
                'finished_by' => auth('sanctum')->id() ?? 0,
                'status' => 'SELESAI',
            ]);
        }

        $this->sptRepository->updateById($sptId, $updateData);

        $fresh = $this->sptRepository->findOrFail($sptId);

        return $this->documentGenerator->generateLaporan($fresh);
    }

    public function getBiayaGrid(int $sptId, int $pegawaiId): array
    {
        $biaya = $this->biayaRepository->findBySptAndPegawai($sptId, $pegawaiId);
        if ($biaya === null) {
            return [[
                'tipe' => 'Total Pengeluaran',
                'biaya' => 0,
                'children' => [],
            ]];
        }

        $rows = $this->biayaRepository->gridRows((int) $biaya->id, $pegawaiId);

        $total = 0;
        foreach ($rows as $r) {
            $total += $r->biaya;
        }

        return [[
            'tipe' => 'Total Pengeluaran',
            'biaya' => $total,
            'children' => $rows,
        ]];
    }

    /**
     * Batch save report rows per pegawai (fixes legacy N+1 problem by
     * pre-loading aggregated child data before the loop).
     */
    public function saveReport(int $sptId, $spt): void
    {
        $details = SPTDetail::where('spt_id', $sptId)->get();

        // Pre-load all data needed to avoid N+1 queries inside the loop.
        $pegawaiMap = Pegawai::whereIn('id', $details->pluck('pegawai_id'))
            ->get()->keyBy('id');
        $biayaMap = Biaya::whereIn('pegawai_id', $details->pluck('pegawai_id'))
            ->where('spt_id', $sptId)->get()->keyBy('pegawai_id');
        $inapMap = Inap::whereIn('pegawai_id', $details->pluck('pegawai_id'))
            ->get()->keyBy('pegawai_id');
        $anggaran = Anggaran::where('id', $spt->anggaran_id)->select('kode_rekening', 'nama_rekening')->first();
        $pengeluaran = Pengeluaran::whereIn('pegawai_id', $details->pluck('pegawai_id'))
            ->whereHas('biaya', fn ($q) => $q->where('spt_id', $sptId))
            ->get()->groupBy('pegawai_id');
        $transports = Transport::whereIn('pegawai_id', $details->pluck('pegawai_id'))
            ->whereHas('biaya', fn ($q) => $q->where('spt_id', $sptId))
            ->get()->groupBy('pegawai_id');

        foreach ($details as $dtl) {
            $biaya = $biayaMap->get($dtl->pegawai_id);
            $userJbtn = $pegawaiMap->get($dtl->pegawai_id);
            $inap = $inapMap->get($dtl->pegawai_id);
            $pengs = $pengeluaran->get($dtl->pegawai_id, collect());
            $trs = $transports->get($dtl->pegawai_id, collect());

            $uangSaku = $this->sumKategori($pengs, 'UANG SAKU');
            $uangMakan = $this->sumKategori($pengs, 'UANG MAKAN');
            $uangRepresentasi = $this->sumKategori($pengs, 'UANG REPRESENTASI');
            $uangDinasDalam = $this->sumKategori($pengs, 'UANG PERJALANAN DINAS DALAM KOTA');
            $uangLain = $pengs->where('total', '>', 0)
                ->filter(fn ($p) => ! str_contains(strtoupper($p->kategori), 'UANG SAKU')
                    && ! str_contains(strtoupper($p->kategori), 'UANG MAKAN')
                    && ! str_contains(strtoupper($p->kategori), 'UANG REPRESENTASI')
                    && ! str_contains(strtoupper($p->kategori), 'UANG PERJALANAN DINAS DALAM KOTA'))
                ->sum('total');

            $pesawatBrgkt = $this->flight($trs, 'Berangkat');
            $pesawatPlg = $this->flight($trs, 'Pulang');

            $this->reportRepository->insertReport([
                'pegawai_id' => $dtl->pegawai_id,
                'spt_id' => $sptId,
                'spt_detail_id' => $dtl->id,
                'biaya_id' => $biaya?->id ?? null,
                'nama_rekening' => $anggaran->nama_rekening ?? null,
                'kode_rekening' => $anggaran->kode_rekening ?? null,
                'nama_pelaksana' => $userJbtn->full_name ?? null,
                'jabatan' => $userJbtn->jabatan ?? null,
                'no_pku' => null,
                'no_spt' => $spt->no_spt,
                'no_sppd' => null,
                'kegiatan' => $spt->untuk,
                'jml_hari' => $spt->jumlah_hari,
                'penyelenggara' => 'SD Dalam Kab. Kerinci',
                'lok_asal' => $spt->daerah_asal,
                'lok_tujuan' => $spt->daerah_tujuan,
                'tgl_berangkat' => $spt->tgl_berangkat,
                'tgl_kembali' => $spt->tgl_kembali,
                'uang_saku' => $uangSaku,
                'uang_makan' => $uangMakan,
                'uang_representasi' => $uangRepresentasi,
                'uang_lain' => $uangLain,
                'uang_dinas_dlm' => $uangDinasDalam,
                'uang_penginapan' => $biaya->total_biaya_inap ?? null,
                'uang_transport' => $biaya->total_biaya_transport ?? null,
                'uang_total' => $biaya->total_biaya ?? null,
                'uang_pesawat' => $biaya->total_biaya_pesawat ?? null,
                'inap_hotel' => $inap->hotel ?? null,
                'inap_room' => $inap->room ?? null,
                'inap_checkin' => $inap->tgl_checkin ?? null,
                'inap_checkout' => $inap->tgl_checkout ?? null,
                'inap_jml_hari' => $inap->jml_hari ?? null,
                'inap_per_malam' => $inap->harga ?? null,
                'inap_jumlah' => $inap->total_bayar ?? null,
                'pesbrgkt_maskapai' => $pesawatBrgkt->agen ?? null,
                'pesbrgkt_no_tiket' => $pesawatBrgkt->no_tiket ?? null,
                'pesbrgkt_kode_booking' => $pesawatBrgkt->kode_booking ?? null,
                'pesbrgkt_no_penerbangan' => $pesawatBrgkt->no_penerbangan ?? null,
                'pesbrgkt_tgl' => $pesawatBrgkt->tgl ?? null,
                'pesbrgkt_jumlah' => $pesawatBrgkt->total_bayar ?? null,
                'peskmbl_maskapai' => $pesawatPlg->agen ?? null,
                'peskmbl_no_tiket' => $pesawatPlg->no_tiket ?? null,
                'peskmbl_kode_booking' => $pesawatPlg->kode_booking ?? null,
                'peskmbl_no_penerbangan' => $pesawatPlg->no_penerbangan ?? null,
                'peskmbl_tgl' => $pesawatPlg->tgl ?? null,
                'peskmbl_jumlah' => $pesawatPlg->total_bayar ?? null,
                'periode' => $spt->periode,
            ]);
        }
    }

    private function sumKategori($pengs, string $needle): float
    {
        return (float) $pengs->filter(fn ($p) => str_contains(strtoupper($p->kategori), $needle))->sum('total');
    }

    private function flight($transports, string $perjalanan): ?object
    {
        return $transports->first(fn ($t) => $t->perjalanan === $perjalanan && $t->jenis_transport === 'Pesawat');
    }

    private function fileIdFromPath(string $path): int
    {
        return (int) DB::table('files')->whereRaw('concat(file_path, \'/\', file_name) = ?', [$path])->value('id');
    }
}
