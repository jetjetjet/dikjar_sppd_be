<?php

namespace App\Services;

use App\Helpers\Utils;
use App\Models\Biaya;
use App\Models\Inap;
use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\Transport;
use App\Repositories\Contracts\FileRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FaFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentGeneratorService
{
    public function __construct(
        protected FileRepositoryInterface $fileRepository,
        protected FileStorageService $fileStorageService,
        protected DocumentMapperService $mapper,
    ) {}

    /**
     * Resolve the absolute path of a template registered in config('sppd.templates').
     * $key is a logical name (e.g. 'sppd', 'kwitansi'), not a raw file name.
     */
    protected function templatePath(string $key): string
    {
        $fileName = config("sppd.templates.{$key}");

        if ($fileName === null) {
            throw new \Exception("Template '{$key}' tidak terdaftar di config/sppd.php.");
        }

        return base_path(config('sppd.template_dir').'/'.$fileName);
    }

    /**
     * Generate SPPD PDFs for each pegawai + the SPT PDF, create biaya rows,
     * and persist files. Returns ['path' => string, 'file_id' => int].
     */
    public function generateProses(SPT $spt, iterable $users): array
    {
        $templatePath = $this->resolveSptTemplate($spt->pttd_id);
        $templateSppdPath = $this->templatePath('sppd');

        if (! FaFile::exists($templateSppdPath)) {
            throw new \Exception('Template SPPD tidak ditemukan');
        }

        $pejabat = $this->getPejabat($spt->pttd_id);
        $sptData = $this->mapper->mapSPT($spt);

        $tempUserValue = [];
        foreach ($users as $user) {
            $tempSppd = new TemplateProcessor($templateSppdPath);
            $tempSppd->setValue('nama_pegawai', $user->nama_pegawai);
            $tempSppd->setValue('jabatan_pegawai', $user->jabatan_pegawai);
            $tempSppd->setValue('golongan_pegawai', str_replace('- -', '-', $user->golongan_pegawai));
            $tempSppd->setValue('untuk', $sptData->untuk);
            $tempSppd->setValue('transportasi', $sptData->transportasi);
            $tempSppd->setValue('jml_hari', $sptData->jml_hari.' Hari');
            $tempSppd->setValue('daerah_asal', $sptData->daerah_asal);
            $tempSppd->setValue('daerah_tujuan', $sptData->daerah_tujuan);
            $tempSppd->setValue('tgl_berangkat', $sptData->tgl_berangkat);
            $tempSppd->setValue('tgl_kembali', $sptData->tgl_kembali);
            $tempSppd->setValue('tgl_sppd', $sptData->tgl_spt);
            $tempSppd->setValue('no_spt', $sptData->no_spt);
            $tempSppd->setValue('nip_pejabat', $pejabat->nip);
            $tempSppd->setValue('jabatan_pejabat', strtoupper($pejabat->jabatan));
            $tempSppd->setValue('golongan_pejabat', $pejabat->pangkat);
            $tempSppd->setValue('nama_pejabat', $pejabat->full_name);

            $newFile = new \stdClass;
            $newFile->dbPath = '/storage/spt/';
            $newFile->ext = '.docx';
            $newFile->originalName = 'SPPD_'.$user->nip_pegawai;

            $fileId = $this->generatePdf($tempSppd, $newFile);

            SPTDetail::where('spt_id', $spt->id)
                ->where('pegawai_id', $user->pegawai_id)
                ->update([
                    'sppd_file_id' => $fileId,
                    'sppd_generated_at' => DB::raw('now()'),
                    'sppd_generated_by' => auth('sanctum')->id(),
                ]);

            $tempUserValue[] = [
                'n-o' => $user->index_no,
                'nama_pegawai' => $user->nama_pegawai,
                'jabatan_pegawai' => $user->jabatan_pegawai,
                'nip_pegawai' => $user->nip_pegawai,
                'pangkat_pegawai' => str_replace('- -', '-', $user->golongan_pegawai),
            ];
        }

        $template = new TemplateProcessor($templatePath);
        $template->setValue('dasar_pelaksana', $sptData->dasar_pelaksana);
        $template->setValue('untuk', $sptData->untuk);
        $template->setValue('nama_pejabat', $pejabat->full_name);
        $template->setValue('nip_pejabat', $pejabat->nip);
        $template->setValue('jabatan_pejabat', strtoupper($pejabat->jabatan));
        $template->setValue('golongan_pejabat', $pejabat->pangkat);
        $template->setValue('tgl_kembali', $sptData->tgl_kembali);
        $template->setValue('tgl_berangkat', $sptData->tgl_berangkat);
        $template->setValue('daerah_tujuan', $sptData->daerah_tujuan);
        $template->setValue('tgl_spt', $sptData->tgl_spt);

        if ($spt->pttd_id > 4) {
            $template->setValue('nomor_surat', $sptData->no_spt);
            $template->cloneRowAndSetValues('n-o', $tempUserValue);
        } else {
            $template->cloneRowAndSetValues('nama_pegawai', $tempUserValue);
        }

        $newFile = new \stdClass;
        $newFile->dbPath = '/storage/spt/';
        $newFile->ext = '.docx';
        $newFile->originalName = 'SPT_Generated';

        $fileId = $this->generatePdf($template, $newFile);
        $record = $this->fileRepository->getById($fileId);

        return ['path' => $record->file_path.'/'.$record->file_name, 'file_id' => $fileId];
    }

    /**
     * Generate the rumming document for a single pegawai.
     */
    public function generateRumming(int $sptId, int $biayaId, int $pegawaiId): string
    {
        $templatePath = $this->templatePath('rumming');
        if (! FaFile::exists($templatePath)) {
            throw new \Exception('Kesalahan! Template tidak ditemukan.');
        }

        $pegawai = Pegawai::where('id', $pegawaiId)
            ->select(DB::raw("coalesce(nip,'-') as nip"), 'full_name as pegawai_name')
            ->first();

        $spt = SPT::join('anggaran as a', 'a.id', '=', 'spt.anggaran_id')
            ->join('pegawai as bdh', 'bdh.id', '=', 'spt.bendahara_id')
            ->join('pegawai as pgn', 'pgn.id', '=', 'spt.pengguna_anggaran_id')
            ->where('spt.id', $sptId)
            ->select(
                'tgl_spt', 'no_spt', 'jumlah_hari', 'daerah_tujuan', 'no_index',
                'pgn.full_name as pengguna_name', 'pgn.nip as pengguna_nip',
                'bdh.full_name as bendahara_name', 'bdh.nip as bendahara_nip',
                'anggaran_id', 'a.bidang'
            )->first();

        $pembantu = $spt->bidang == 'Staf Sekretariat' ? 'Bendahara Pengeluaran,' : 'Bendahara Pengeluaran Pembantu,';
        $labelPengguna = $spt->bidang == 'Staf Sekretariat' ? 'Pengguna Anggaran,' : 'Kuasa Pengguna Anggaran,';
        $nameFile = '090_'.$spt->no_index.'_SPPD_PDK_'.$pegawaiId;

        $biayaTb = [];

        $pengeluaran = Pengeluaran::where('biaya_id', $biayaId)
            ->where('pegawai_id', $pegawaiId)
            ->groupBy('kategori')->groupBy('catatan')
            ->select(
                'kategori as pengeluaran',
                DB::raw('sum(jml) as qty'),
                DB::raw('sum(nominal) as harga'),
                DB::raw("string_agg(catatan, ', ') as catatan")
            )->get();
        foreach ($pengeluaran as $p) {
            $biayaTb[] = $this->mapper->mapBiaya($p);
        }

        $transport = Transport::where('biaya_id', $biayaId)
            ->where('pegawai_id', $pegawaiId)
            ->groupBy('jenis_transport')->groupBy('perjalanan')->groupBy('agen')
            ->select(
                DB::raw("jenis_transport || ' ' || perjalanan || ' ' || agen as pengeluaran"),
                DB::raw('sum(1) as qty'),
                DB::raw('sum(total_bayar) as harga'),
                DB::raw("string_agg(catatan, ', ') as catatan")
            )->get();
        foreach ($transport as $t) {
            $biayaTb[] = $this->mapper->mapBiaya($t);
        }

        $inap = Inap::where('biaya_id', $biayaId)
            ->where('pegawai_id', $pegawaiId)
            ->select(
                DB::raw("'Penginapan ' || hotel as pengeluaran"),
                'jml_hari as qty', 'harga', 'catatan'
            )->get();
        foreach ($inap as $i) {
            $biayaTb[] = $this->mapper->mapBiaya($i);
        }

        $grandTotal = array_sum(array_map(fn ($val) => $val->totalRaw, $biayaTb));
        $terbilang = Utils::rupiahTeks($grandTotal);
        $tgl = (new Carbon($spt->tgl_spt))->isoFormat('D MMMM Y');

        $template = new TemplateProcessor($templatePath);
        $template->setValue('jumlah', number_format($grandTotal));
        $template->setValue('terbilang', $terbilang);
        $template->setValue('no_spt', $spt->no_spt);
        $template->setValue('tgl', $tgl);
        $template->setValue('jml_hari', $spt->jumlah_hari);
        $template->setValue('daerah_tujuan', $spt->daerah_tujuan);
        $template->setValue('nama_bendahara', $spt->bendahara_name);
        $template->setValue('nip_bendahara', $spt->bendahara_nip);
        $template->setValue('nama_pengguna', $spt->pengguna_name);
        $template->setValue('nip_pengguna', $spt->pengguna_nip);
        $template->setValue('bendahara', $pembantu);
        $template->setValue('label_pengguna', $labelPengguna);
        $template->setValue('nama_penerima', $pegawai->pegawai_name);
        $template->setValue('nip_penerima', $pegawai->nip);
        $template->cloneRowAndSetValues('pengeluaran', $biayaTb);

        $newFile = new \stdClass;
        $newFile->dbPath = '/storage/rumming/';
        $newFile->ext = '.docx';
        $newFile->originalName = 'rumming_'.$nameFile;

        return $this->generatePdfAndReturnPath($template, $newFile);
    }

    /**
     * Generate the kwitansi document and return its file path string.
     */
    public function generateKwitansi(int $sptId): string
    {
        $templatePath = $this->templatePath('kwitansi');
        if (! FaFile::exists($templatePath)) {
            throw new \Exception('Template SPT tidak ditemukan.');
        }

        $spt = SPT::join('anggaran as a', 'a.id', '=', 'spt.anggaran_id')
            ->join('pegawai as p', 'p.id', '=', 'spt.pelaksana_id')
            ->join('pegawai as bdh', 'bdh.id', '=', 'spt.bendahara_id')
            ->join('pegawai as pgn', 'pgn.id', '=', 'spt.pengguna_anggaran_id')
            ->join('pegawai as pptk', 'pptk.id', '=', 'spt.pptk_id')
            ->where('spt.id', $sptId)
            ->select(
                'kode_rekening', 'nama_rekening', 'spt.periode', 'dasar_pelaksana',
                'daerah_asal', 'daerah_tujuan', 'tgl_berangkat', 'tgl_kembali',
                'untuk', 'tgl_spt', 'no_spt',
                'p.full_name as nama_penyelenggara', 'p.nip as nip_pengelenggara',
                'no_index', 'pgn.full_name as pengguna_name', 'pgn.nip as pengguna_nip',
                'bdh.full_name as bendahara_name', 'bdh.nip as bendahara_nip',
                'pptk.full_name as pptk_name', 'pptk.nip as pptk_nip',
                'a.bidang', 'spt.anggaran_id'
            )->first();

        $nameFile = time().'_090_'.$spt->no_index;
        // Fixed duplicated sum query (was computed twice in legacy).
        $totalBiaya = Biaya::where('spt_id', $sptId)->sum('total_biaya');

        $labelPengguna = $spt->bidang == 'Staf Sekretariat' ? 'Pengguna Anggaran,' : 'Kuasa Pengguna Anggaran,';
        $pembantu = $spt->bidang == 'Staf Sekretariat' ? 'Bendahara Pengeluaran, </w:t><w:br/><w:t>' : 'Bendahara Pengeluaran Pembantu,';
        $bendaharaJbtn = $spt->bidang == 'Staf Sekretariat' ? 'Bendahara Pengeluaran' : 'Bendahara Pengeluaran Pembantu';

        $terbilang = Utils::rupiahTeks($totalBiaya);
        $tgl = (new Carbon($spt->tgl_spt))->isoFormat('D MMMM Y');

        $template = new TemplateProcessor($templatePath);
        $template->setValue('tahun_anggaran', $spt->periode);
        $template->setValue('kode_rekening', $spt->kode_rekening);
        $template->setValue('nama_rekening', $spt->nama_rekening);
        $template->setValue('bendahara_terima', $bendaharaJbtn);
        $template->setValue('nama_bendahara', $spt->bendahara_name);
        $template->setValue('nip_bendahara', $spt->bendahara_nip);
        $template->setValue('bendahara', $pembantu);
        $template->setValue('total_biaya', number_format($totalBiaya));
        $template->setValue('terbilang', $terbilang);
        $template->setValue('maksud', $spt->untuk);
        $template->setValue('no_spt', $spt->no_spt);
        $template->setValue('tgl_spt', $tgl);
        $template->setValue('label_pengguna', $labelPengguna);
        $template->setValue('nama_pengguna', $spt->pengguna_name);
        $template->setValue('nip_pengguna', $spt->pengguna_nip);
        $template->setValue('nama_pptk', $spt->pptk_name);
        $template->setValue('nip_pptk', $spt->pptk_nip);
        $template->setValue('nama_penerima', $spt->nama_penyelenggara);
        $template->setValue('nip_penerima', $spt->nip_pengelenggara);

        $newFile = new \stdClass;
        $newFile->dbPath = '/storage/kwitansi/';
        $newFile->ext = '.docx';
        $newFile->originalName = 'kwitansi_'.$nameFile;

        return $this->generatePdfAndReturnPath($template, $newFile);
    }

    /**
     * Generate the laporan document and return its file path string.
     */
    public function generateLaporan(SPT $spt): string
    {
        $templatePath = $this->templatePath('laporan');
        if (! FaFile::exists($templatePath)) {
            throw new \Exception('Template Laporan SPT tidak ditemukan.');
        }

        $pelaksana = DB::table('pegawai as p')->where('id', $spt->pelaksana_id)
            ->select('nip', 'full_name as nama', 'jabatan', DB::raw("pangkat || ' ' || golongan as pangkat"))
            ->first();

        $nameFile = time().'_lprn_090_'.($spt->no_index ?? '');

        $tglCetak = (new Carbon)->isoFormat('D MMMM Y');
        $tglAwal = (new Carbon($spt->tgl_berangkat))->isoFormat('D MMMM Y');
        $tglAkhir = (new Carbon($spt->tgl_kembali))->isoFormat('D MMMM Y');

        $users = SPTDetail::join('pegawai as p', 'p.id', '=', 'spt_detail.pegawai_id')
            ->where('spt_id', $spt->id)
            ->select('is_pelaksana', 'full_name as nama_pegawai')
            ->orderBy('is_pelaksana', 'DESC')->orderBy('nip')->orderBy('full_name')
            ->get();

        $tempUserValue = [];
        $tempPengikut = [];
        $countPengikut = 1;
        foreach ($users as $key => $user) {
            if ($user->is_pelaksana == false) {
                $tempPengikut[] = ['np' => $countPengikut, 'nama_pengikut' => $user->nama_pegawai];
                $countPengikut++;
            }
            $tempUserValue[] = ['np' => $key + 1, 'nama_pengikut' => $user->nama_pegawai];
        }

        $template = new TemplateProcessor($templatePath);
        $template->setValue('tgl_cetak', $tglCetak);
        $template->setValue('nama_pelaksana', $pelaksana->nama);
        $template->setValue('nip_pelaksana', $pelaksana->nip);
        $template->setValue('pangkat_pelaksana', $pelaksana->pangkat);
        $template->setValue('jabatan_pelaksana', $pelaksana->jabatan);
        $template->setValue('no_spt', $spt->no_spt);
        $template->setValue('tgl_dinas', $tglAwal.' s.d '.$tglAkhir);
        $template->setValue('tujuan', $spt->daerah_tujuan);
        $template->setValue('maksud', $spt->untuk);
        $template->setValue('saran', strip_tags($spt->saran));

        $tHasil = [];
        $hasil = explode('<li>', $spt->hasil);
        $ctr = 1;
        foreach ($hasil as $hsl) {
            $vHsl = strip_tags(html_entity_decode($hsl, ENT_COMPAT, 'UTF-8'));
            if (! empty($vHsl)) {
                $tHasil[] = ['nh' => $ctr, 'hasil' => strip_tags($hsl)];
                $ctr++;
            }
        }

        $template->cloneRowAndSetValues('np', $tempPengikut);
        $template->cloneRowAndSetValues('nh', $tHasil);
        $template->cloneRowAndSetValues('np', $tempUserValue);

        $newFile = new \stdClass;
        $newFile->dbPath = '/storage/laporan/';
        $newFile->ext = '.docx';
        $newFile->originalName = $nameFile;

        return $this->generatePdfAndReturnPath($template, $newFile);
    }

    // --- internal helpers ---

    protected function resolveSptTemplate(int $pttdId): string
    {
        if ($pttdId == 2 || $pttdId == 3) {
            return $this->templatePath('spt_bupati');
        }
        if ($pttdId == 4) {
            return $this->templatePath('spt_sekda');
        }
        if ($pttdId == 219) {
            return $this->templatePath('spt_default');
        }

        return $this->templatePath('spt_an');
    }

    protected function getPejabat(int $pttdId): object
    {
        return Pegawai::where('pegawai.id', $pttdId)
            ->select('full_name', 'nip', 'jabatan', 'pangkat', 'golongan')
            ->first();
    }

    /**
     * Fill a template, save it as .docx, persist its file record and return file id.
     *
     * Ditulis lewat Storage::disk('public') (bukan base_path('public') langsung)
     * supaya fisiknya selalu jatuh di storage/app/public — sama dengan lokasi
     * yang di-mount sebagai named volume di production — dan tidak bergantung
     * pada symlink public/storage sudah terbentuk atau belum.
     */
    protected function generatePdf(TemplateProcessor $template, \stdClass $file): int
    {
        $subFolder = trim(str_replace('/storage/', '', $file->dbPath), '/');
        $disk = Storage::disk('public');
        $disk->makeDirectory($subFolder);

        $newName = time().'_'.$file->originalName.'.docx';
        $template->saveAs($disk->path($subFolder.'/'.$newName), true);

        return $this->fileRepository->saveFile(
            $newName,
            $file->originalName,
            $file->dbPath,
            ltrim($file->ext, '.')
        );
    }

    /**
     * Fill a template, save it as .docx, and return the public path string (file_path + file_name).
     */
    protected function generatePdfAndReturnPath(TemplateProcessor $template, \stdClass $file): string
    {
        $fileId = $this->generatePdf($template, $file);
        $record = $this->fileRepository->getById($fileId);

        return $record->file_path.'/'.$record->file_name;
    }
}
