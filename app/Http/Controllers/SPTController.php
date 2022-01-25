<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\ReportSPPD;
use App\Models\Pegawai;
use App\Models\Biaya;
use App\Models\Inap;
use App\Models\SPTLog;
use App\Models\Transport;
use App\Models\Pengeluaran;
use App\Models\Jabatan;
use App\Models\SPTGuest;
use App\Helpers\Utils;
use Auth;

use DB;
use Validator;
use Carbon\Carbon;

use App\Helpers\MyPdf;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\File as FaFile;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use NcJoes\OfficeConverter\OfficeConverter;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SPTController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;

		$user = $request->user();
		$isAdmin = $user->tokenCan('is_admin') ? 1 : 0;
		$canGenerate = $user->tokenCan('spt_generate') || $isAdmin == 1 ? 1 : 0;

		$pegawai = SPTDetail::join('pegawai as p', 'p.id', 'spt_detail.pegawai_id')
		->join('spt', 'spt.id', 'spt_detail.spt_id')
		->groupBy('spt_detail.spt_id')
		->select(
			'spt_id',
			DB::raw("string_agg(p.full_name, '_') as name")
		);

		$q = SPT::joinSub($pegawai, 'u', function ($join) {
			$join->on('spt.id', 'u.spt_id');
		});

		if (!$isAdmin) {
			$loginid = auth('sanctum')->user()->id;
			$q = $q->where('spt.created_by', $loginid);
		}
		
		$q = $q->select(
			'id',
			'no_spt',
			'jenis_dinas',
			DB::raw("tgl_berangkat || ' s/d ' || tgl_kembali as tgl"),
			'daerah_tujuan',
			'untuk',
			DB::raw("case when spt_file_id is not null then true else false end as spt_file"),
			'settled_at',
			'proceed_at',
			'u.name',
			DB::raw($canGenerate . " as can_generate")
		)->orderBy('tgl_spt', 'DESC');

		$results['data'] = $q->get();

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function viewSPT(Request $request)
	{
		$results = $this->responses;

		$q = SPTGuest::join('spt', 'spt_id', 'spt.id')
		->where('key', $inputs['key'])
		->first();

		if($q != null) {
			$data = $this->mapSPT($q);

			$data->pegawai = SPTDetail::join('pegawai as p', 'pegawai_id', 'p.id')
			->where('spt_id', $data['spt_id'])
			->select(
				'full_name',
				DB::raw("coalesce(nip, '-') as nip"),
				DB::raw("coalesce(pangkat, '-') as pangkat"),
				DB::raw("coalesce(golongan, '-') as golongan")
			)->get();

			$results['state_code'] = 200;
			$results['success'] = true;
			$results['data'] = $data;
		} else {
			array_push($results['messages'], 'Data tidak ditemukan!.');
		}

		return response()->json($results, $results['state_code']);
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

	public function proses($id)
	{
		$results = $this->responses;
		$spt = SPT::find($id);

		if($spt->proceed_at == null) {
			// $labelDoc = 
			$templatePath = base_path('public/storage/template/template_spt.docx');
			if($spt->pttd_id == 2 || $spt->pttd_id == 3) {
				$templatePath = base_path('public/storage/template/template_spt_bupati.docx');
			} else if ($spt->pttd_id == 4) {
				$templatePath = base_path('public/storage/template/template_spt_sekda.docx');
			} else {
				$templatePath = base_path('public/storage/template/template_spt.docx');
			}

			$checkFile = FaFile::exists($templatePath);
			if ($checkFile){
				$pejabatId = $spt->pttd_id == 2 || $spt->pttd_id == 3 || $spt->pttd_id == 4 ? $spt->pttd_id : 5;

				$pejabat = Pegawai::where('pegawai.id', $pejabatId)
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
					foreach($users as $user){
						$tempSppd = new TemplateProcessor($templateSppdPath);

						// $template->setValue('dasar_pelaksana', $spt->dasar_pelaksana);
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
						// $template->setValue('tgl_berangkat_plus', $brgktPlus->isoFormat('D MMMM Y'));
						// $template->setValue('tgl_kembali_minus', $kembaliMinus->isoFormat('D MMMM Y'));
						$tempSppd->setValue('tgl_sppd', $sptData->tgl_spt);
						$tempSppd->setValue('no_spt', $sptData->no_spt);

						// KADIN
						$tempSppd->setValue('nama_kadin', $pejabat->full_name);
						$tempSppd->setValue('nip_kadin', $pejabat->nip);
						$tempSppd->setValue('golongan_kadin', $pejabat->pangkat);

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
						$file = Utils::saveFile($newFile);

						// update table spt
						SPTDetail::where('spt_id', $id)
						->where('pegawai_id', $user->pegawai_id)
						->update([
							'sppd_file_id' => $file,
							'sppd_generated_at' => DB::raw("now()"),
							'sppd_generated_by' => auth('sanctum')->user()->id,
						]);

						//create biaya awal
						Biaya::create([
							'spt_id' => $id,
							'pegawai_id' => $user->pegawai_id,
							'total_biaya_lainnya' => 0,
							'total_biaya_inap' => 0,
							'total_biaya_travel' => 0,
							'total_biaya' => 0,
						]);
						
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
					$file = Utils::saveFile($newFile);
					// update
					$loginId = auth('sanctum')->user()->id;
					$spt->update([
						'proceed_at' => DB::raw("now()"),
						'proceed_by' => $loginId,
						'spt_file_id' => $file,
						'spt_generated_at' => DB::raw("now()"),
						'spt_generated_by' => $loginId,
						'status' => 'PROCEED'
					]);

					//save to log
					SPTLog::create([
						'user_id' => $loginId,
						'username' => auth('sanctum')->user()->pegawai->full_name,
						'reference_id' => $id,
						'aksi' => 'Proses SPT',
						'success' => '1'
					]);

					//commit to DB
					DB::commit();
					$results['data'] = $newFile->dbPath . $newFile->newName;
					array_push($results['messages'], 'Berhasil memproses SPT.');
					$results['success'] = true;
					$results['state_code'] = 200;
				} catch (\Exception $e) {
					DB::rollBack();
					Log::channel('spderr')->info('spt_proses: '. json_encode($e->getMessage()));
					array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
				}
			}
		}
		
		return response()->json($results, $results['state_code']);
	}

	public function cetakSpt($id)
	{
		$results = $this->responses;
		$spt = SPT::find($id);
		if($spt->spt_file_id != null) {
			$file = DB::table('files')->where('id', $spt->spt_file_id)->first();
			$results['data'] = $file->file_path . $file->file_name;
			$results['success'] = true;
			$results['state_code'] = 200;
		}
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'anggaran_id' => 'required',
			'jenis_dinas'=> 'required', 
      'pttd_id' => 'required',
      'pptk_id' => 'required',
      'bendahara_id' => 'required',
      'tgl_spt' => 'required',
      'pelaksana_id' => 'required',
      'dasar_pelaksana' => 'required',
      'untuk' => 'required',
      'transportasi' => 'required',
      'tgl_berangkat' => 'required',
      'tgl_kembali' => 'required',
			'daerah_asal' => 'required',
			'daerah_tujuan' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			DB::transaction(function () use ($inputs) {
				$brgkt = new Carbon($inputs['tgl_berangkat']);
				$kembali = new Carbon($inputs['tgl_kembali']);
				$jumlahHari = $brgkt->diff($kembali)->days;

				if(Carbon::now()->format('d-m') == '01-01') {
					$noMax = 1;
				} else {
					$noMax = SPT::whereNull('deleted_at')->max('no_index') + 1 ?? 1;
				}
				
				$tahun = Carbon::now()->format('Y');
				$noSpt = '090/'. str_pad($noMax, 3, '0', STR_PAD_LEFT) . '/SPT/PDK/' . $tahun ;
				$spt = SPT::create([
					'no_index' => $noMax,
					'no_spt' => $noSpt,
					// 'bidang_id' => $inputs['bidang_id'],
					'jenis_dinas' => $inputs['jenis_dinas'],
					'anggaran_id' => $inputs['anggaran_id'],
					'pttd_id' => $inputs['pttd_id'],
					'pptk_id' => $inputs['pptk_id'],
					'pelaksana_id' => $inputs['pelaksana_id'],
					'bendahara_id' => $inputs['bendahara_id'],
					'dasar_pelaksana' => $inputs['dasar_pelaksana'],
					'untuk' => $inputs['untuk'],
					'transportasi' => $inputs['transportasi'],
					'daerah_asal' => $inputs['daerah_asal'],
					'daerah_tujuan' => $inputs['daerah_tujuan'],
					'tgl_berangkat' => $inputs['tgl_berangkat'],
					'tgl_kembali' => $inputs['tgl_kembali'],
					'jumlah_hari' => $jumlahHari + 1,
					'tgl_spt' => $inputs['tgl_spt'],
					'status' => 'DRAFT',
					'periode' => date('Y')
				]);

				SPTDetail::create([
					'spt_id' => $spt->id,
					'pegawai_id' => $inputs['pelaksana_id'],
					'is_pelaksana' => '1'
				]);

				foreach($inputs['pegawai_id'] as $pegawaiId){
					$detail = SPTDetail::create([
						'spt_id' => $spt->id,
						'pegawai_id' => $pegawaiId,
						'is_pelaksana' => '0'
					]);
				}

				//save to log
				SPTLog::create([
					'user_id' => auth('sanctum')->user()->id,
					'username' => auth('sanctum')->user()->pegawai->full_name,
					'reference_id' => $spt->id,
					'aksi' => 'Simpan SPT',
					'success' => '1'
				]);
			});
	
			array_push($results['messages'], 'Berhasil menambahkan SPT baru.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('spt_save_err: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}

		return response()->json($results, $results['state_code']);
	}

	public function finish($id)
	{
		$results = $this->responses;
		$finish = array(
			'completed_at' => DB::raw("now()"),
			'completed_by' => auth('sanctum')->user()->id
		);

		$spt = SPT::where('id', $id)->first();
		$spt->update($finish);
		// SPTDetail::where('spt_id', $id)->update($finish);
		
		//save to log
		SPTLog::create([
			'user_id' => auth('sanctum')->user()->id,
			'username' => auth('sanctum')->user()->pegawai->full_name,
			'reference_id' => $id,
			'aksi' => 'Selesai SPT',
			'success' => '1'
		]);

		array_push($results['messages'], 'Perjalanan Dinas berhasil diselesaikan.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$data = SPT::join('anggaran as ag', 'ag.id', 'anggaran_id')
		->where('spt.id', $id)
		->select(
			'spt.*',
			'ag.kode_rekening as anggaran_text'
		)->first();

		$data->pegawai_id = SPTDetail::where('spt_id', $id)->where('is_pelaksana', '0')->get()->pluck('pegawai_id');
		$results['data'] = $data;

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function getSPT($id)
	{
		$results = $this->responses;
		$data = SPT::join('files as f', 'f.id', 'spt_file_id')
		->where('spt.id', $id)
		->first();

		if($data != null){
			$results['data'] = $data->file_path . $data->file_name;
		} else {
			array_push($results['message'], 'SPT tidak ditemukan!');
		}
		return response()->json($results, $results['state_code']);
	}
	
	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			// 'bidang_id' => 'required',
			'jenis_dinas' => 'required',
      'anggaran_id' => 'required',
      'pttd_id' => 'required',
      'pptk_id' => 'required',
      'dasar_pelaksana' => 'required',
      'untuk' => 'required',
      'transportasi' => 'required',
			'pelaksana_id' => 'required',
      'tgl_berangkat' => 'required',
      'tgl_kembali' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 409);
    }

		try {
			DB::transaction(function () use ($inputs, $id) {
				$spt = SPT::find($id);
				
				$brgkt = new Carbon($inputs['tgl_berangkat']);
				$kembali = new Carbon($inputs['tgl_kembali']);
				$jumlahHari = $brgkt->diff($kembali)->days;

				$updateSpt = $spt->update([
					'jenis_dinas' => $inputs['jenis_dinas'],
					'anggaran_id' => $inputs['anggaran_id'],
					'pttd_id' => $inputs['pttd_id'],
					'pptk_id' => $inputs['pptk_id'],
					'pelaksana_id' => $inputs['pelaksana_id'],
					'bendahara_id' => $inputs['bendahara_id'],
					'dasar_pelaksana' => $inputs['dasar_pelaksana'],
					'untuk' => $inputs['untuk'],
					'transportasi' => $inputs['transportasi'],
					'daerah_asal' => $inputs['daerah_asal'],
					'daerah_tujuan' => $inputs['daerah_tujuan'],
					'tgl_berangkat' => $inputs['tgl_berangkat'],
					'tgl_kembali' => $inputs['tgl_kembali'],
					'jumlah_hari' => $jumlahHari + 1,
					'tgl_spt' => $inputs['tgl_spt'],
				]);

				//Delete Missing Pegawai Id
				array_push($inputs['pegawai_id'], $inputs['pelaksana_id']);
				$data = SPTDetail::where('spt_id', $id)
				->whereNotIn('pegawai_id', $inputs['pegawai_id'])
				// ->where('is_pelaksana', '0')
				->delete();

				//check if is pelaksana null
				$validasiPelaksana = SPTDetail::where('spt_id', $id)->where('is_pelaksana', '1')->first();

				if($validasiPelaksana == null) {
					SPTDetail::create([
						'spt_id' => $spt->id,
						'pegawai_id' => $inputs['pelaksana_id'],
						'is_pelaksana' => '1'
					]);
				}

				//Insert new or skip
				foreach($inputs['pegawai_id'] as $pegawaiId){
					$detailSPT = SPTDetail::where('pegawai_id', $pegawaiId)->where('spt_id', $id)->first();
					if ($detailSPT == null ){
						$detail = SPTDetail::create([
							'spt_id' => $id,
							'pegawai_id' => $pegawaiId,
							'is_pelaksana' => '0'
						]);
					}
				}
				
				//save to log
				SPTLog::create([
					'user_id' => auth('sanctum')->user()->id,
					'username' => auth('sanctum')->user()->pegawai->full_name,
					'reference_id' => $id,
					'aksi' => 'Ubah SPT',
					'success' => '1'
				]);
			});
	
			array_push($results['messages'], 'Berhasil mengubah SPT.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e){
			Log::channel('spderr')->info('spt_update_err: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$header = SPT::find($id);
		if ($header->status == 'DRAFT'){
			try{
				DB::transaction(function () use ($id) {
					$detail = SPTDetail::where('spt_id', $id)->delete();
					$header = SPT::find($id)->delete();
				});

				//save to log
				SPTLog::create([
					'user_id' => auth('sanctum')->user()->id,
					'username' => auth('sanctum')->user()->pegawai->full_name,
					'reference_id' => $id,	
					'aksi' => 'Hapus SPT',
					'success' => '1'
				]);
				array_push($results['messages'], 'Berhasil menghapus data.');
				$results['state_code'] = 200;
				$results['success'] = true;
			} catch(\Exception $e){
				Log::channel('spderr')->info('spt_delete_err: '. json_encode($e->getMessage()));
				array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
			}
		} else {
			array_push($results['messages'], 'SPT tidak dapat dihapus!');
		}

		return response()->json($results, $results['state_code']);
	}
}
