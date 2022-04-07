<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\Pegawai;
use App\Helpers\Utils;
use Carbon\Carbon;
use App\Models\File as FileModel;

use DB;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\File as FaFile;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use NcJoes\OfficeConverter\OfficeConverter;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateSurat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surat:generate {--spt=} {--sppd=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $id = $this->option('spt');
        if($id){
            $spt = SPT::find($id);
            $templatePath = base_path('public/storage/template/template_spt.docx');
            if($spt->pttd_id == 2 || $spt->pttd_id == 3) {
                $templatePath = base_path('public/storage/template/template_spt_bupati.docx');
            } else if ($spt->pttd_id == 4) {
                $templatePath = base_path('public/storage/template/template_spt_sekda.docx');
            } else if ($spt->pttd_id == 5) {
                $templatePath = base_path('public/storage/template/template_spt.docx');
            } else {
                $templatePath = base_path('public/storage/template/template_spt_an.docx');
            }
    
            $checkFile = FaFile::exists($templatePath);
            if ($checkFile){
                $pejabat = Pegawai::where('pegawai.id', $spt->pttd_id)
                ->select(
                    'full_name',
                    'nip',
                    'jabatan',
                    'pangkat',
                    'golongan'
                )->first();

                $users = SPTDetail::join('pegawai as p', 'p.id', 'spt_detail.pegawai_id')
                ->where('spt_id',$id)
                ->select(
                    DB::raw("ROW_NUMBER() OVER (ORDER BY spt_detail.id) AS index_no"), 
                    'full_name as nama_pegawai', 
                    'jabatan as jabatan_pegawai', 
                    DB::raw("pangkat || ' ' || golongan as golongan_pegawai"),
                    'spt_detail.id',
                    'pegawai_id',
                    'nip as nip_pegawai')
                ->get();
                
                $sptData = $this->mapSPT($spt);
                try {
                    $userValue = array();
                    $templateSppdPath = base_path('public/storage/template/template_sppd.docx');
                    $sppdFile = FaFile::exists($templateSppdPath);
                    
                    if(!$sppdFile) {
                        throw new \Exception('Template SPPD tidak ditemukan');
                    }

                    //Begin Transaction
                    DB::beginTransaction();

                    $tempUserValue = array();

                    $sppdId = $this->option('sppd');

                    foreach($users as $user){
                        if($sppdId){
                            $tempSppd = new TemplateProcessor($templateSppdPath);

                            $tempSppd->setValue('nama_pegawai', $user->nama_pegawai);
                            $tempSppd->setValue('jabatan_pegawai', $user->jabatan_pegawai);
                            $tempSppd->setValue('golongan_pegawai', str_replace("- -","-", $user->golongan_pegawai));
                            $tempSppd->setValue('untuk', $sptData->untuk);
                            $tempSppd->setValue('transportasi', $sptData->transportasi);
                            $tempSppd->setValue('jml_hari', $sptData->jml_hari . " Hari");
                            $tempSppd->setValue('daerah_asal', $sptData->daerah_asal);
                            $tempSppd->setValue('daerah_tujuan', $sptData->daerah_tujuan);
                            $tempSppd->setValue('tgl_berangkat', $sptData->tgl_berangkat);
                            $tempSppd->setValue('tgl_kembali', $sptData->tgl_kembali);
                            $tempSppd->setValue('tgl_sppd', $sptData->tgl_spt);
                            $tempSppd->setValue('no_spt', $sptData->no_spt);
    
                            $newFile = new \stdClass();
                            $newFile->dbPath ='/storage/spt/';
                            $newFile->ext = '.pdf';
                            $newFile->originalName = "SPPD_" . $user->nip_pegawai;
                            $newFile->newName = time()."_".$newFile->originalName;
    
                            $path = base_path('/public');
                            $tempSppd->saveAs($path . $newFile->dbPath . $newFile->newName . ".docx", TRUE);
                            //Convert kwe PDF
                            $docPath = $path . $newFile->dbPath . $newFile->newName . ".docx";
                            $converter = new OfficeConverter($docPath);
                            //generates pdf file in same directory as test-file.docx
                            $converter->convertTo($newFile->newName.".pdf");
                            
                            $oldFile = $path . $newFile->dbPath . $newFile->newName . ".docx";
                            if(FaFile::exists($oldFile)) {
                                FaFile::delete($oldFile);
                            }
    
                            $newFile->newName = $newFile->newName.".pdf";
    
                            //upload to table
                            $file = FileModel::create([
                                'file_name' => $newFile->newName,
                                'original_name' =>  $newFile->originalName,
                                'file_path' => $newFile->dbPath,
                                'ext' => $newFile->ext,
                            ]);
    
                            // update table spt
                            SPTDetail::where('spt_id', $id)
                            ->where('pegawai_id', $user->pegawai_id)
                            ->update([
                                'sppd_file_id' => $file->id,
                            ]);
                        }
                        
                        $temp = array(
                            'n-o' => $user->index_no,
                            'nama_pegawai' => $user->nama_pegawai,
                            'jabatan_pegawai' => $user->jabatan_pegawai,
                            'nip_pegawai' => $user->nip_pegawai,
                            'pangkat_pegawai' => str_replace("- -","-", $user->golongan_pegawai)
                        );
                        array_push($tempUserValue, $temp);
                    }

                    // Temp Template
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

                        //generate QRCode
                        $uuid = (string) Str::uuid();
                        $uuidSplit = explode('-', $uuid);
                        QrCode::format('png')->generate('https://sppd.disdikkerinci.id/verifikasi?key='. $uuidSplit[0] , base_path('public/storage/images/spt_qr.png'));
                        $template->setImageValue('QRCODE', base_path('public/storage/images/spt_qr.png'));
                        
                        $sptGuest = DB::table('spt_guest')->insert([
                            'spt_id' => $sptData->id,
                            'key' => $uuidSplit[0]
                        ]);
                    } else {
                        $template->cloneRowAndSetValues('nama_pegawai', $tempUserValue);
                    }
                    
                    $newFile = new \stdClass();
                    $newFile->dbPath ='/storage/spt/';
                    $newFile->ext = '.pdf';
                    $newFile->originalName = "SPT_Generated";
                    $newFile->newName = time()."_".$newFile->originalName;

                    $path = base_path('/public');
                    $template->saveAs($path . $newFile->dbPath . $newFile->newName . ".docx", TRUE);
                    //Convert kwe PDF
                    $docPath = $path . $newFile->dbPath . $newFile->newName . ".docx";
                    $converter = new OfficeConverter($docPath);
                    //generates pdf file in same directory as test-file.docx
                    $converter->convertTo($newFile->newName.".pdf");

                    $oldFile = $path . $newFile->dbPath . $newFile->newName . ".docx";
                    if(FaFile::exists($oldFile)) {
                        FaFile::delete($oldFile);
                    }

                    //rename filename
                    $newFile->newName = $newFile->newName.".pdf";
                    
                    //save to table
                    $file = FileModel::create([
                        'file_name' => $newFile->newName,
                        'original_name' =>  $newFile->originalName,
                        'file_path' => $newFile->dbPath,
                        'ext' => $newFile->ext,
                    ]);
                    // Utils::saveFile($newFile);
                    // update
                    $spt->update([
                        'spt_file_id' => $file->id,
                        'spt_generated_at' => DB::raw("now()"),
                        'spt_generated_by' => 0
                    ]);

                    //commit to DB
                    DB::commit();
                    print_r('oke');
                } catch (\Exception $e) {
                    DB::rollBack();
                    print_r($e);
                }
            }
        }
    }

    function mapSPT($db)
	{
		$ui = new \stdClass();
		$ui->id = isset($db->id) ? $db->id : "";
		$ui->jml_hari = isset($db->jumlah_hari) ? $db->jumlah_hari : "";
		$ui->tgl_berangkat = isset($db->tgl_berangkat) ? (new Carbon($db->tgl_berangkat))->isoFormat('D MMMM Y') : "";
		$ui->tgl_kembali = isset($db->tgl_kembali) ? (new Carbon($db->tgl_kembali))->isoFormat('D MMMM Y') : "";
		$ui->tgl_spt = isset($db->tgl_spt) ? (new Carbon($db->tgl_spt))->isoFormat('D MMMM Y') : "";
		$ui->daerah_asal = $db->daerah_asal;
		$ui->daerah_tujuan = $db->daerah_tujuan;
		$ui->no_spt = isset($db->no_spt) ? $db->no_spt : "";
		$ui->periode = isset($db->periode) ? $db->periode : "";
		$ui->no_index = isset($db->no_index) ? $db->no_index : "";
		$ui->untuk = isset($db->untuk) ? $db->untuk : "";
		$ui->transportasi = isset($db->transportasi) ? $db->transportasi : "";
		$ui->dasar_pelaksana = isset($db->dasar_pelaksana) ? $db->dasar_pelaksana : "";

		return $ui;
	}
}
