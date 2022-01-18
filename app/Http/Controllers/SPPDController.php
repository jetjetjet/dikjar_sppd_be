<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\Pegawai;
use App\Models\Transport;
use App\Models\Inap;
use App\Models\Biaya;
use App\Models\Pengeluaran;
use App\Models\ReportSPPD;
use App\Models\SPTLog;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File as FaFile;
use App\Helpers\Utils;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use NcJoes\OfficeConverter\OfficeConverter;

class SPPDController extends Controller
{
	public function grid(Request $request, $id)
	{
		$results = $this->responses;
		$user = $request->user();
		$isAdmin = $user->tokenCan('is_admin') ? 1 : 0;
		$loginid = $user->id;
		$canGenerate = $isAdmin == 1 || $user->tokenCan('sppd-generate') ? 1 : 0;

		$header = SPT::where('spt.id', $id)
		->join('pegawai as b', 'b.id', 'bendahara_id')
		->join('pegawai as pptk', 'pptk.id', 'pptk_id')
		->join('pegawai as pttd', 'pttd.id', 'pttd_id')
		->join('pegawai as pel', 'pel.id', 'pelaksana_id')
		->join('anggaran as ang', 'ang.id', 'anggaran_id')
		->select(
			'no_spt',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tgl_kembali"),
			'daerah_asal',
			'daerah_tujuan',
			'transportasi',
			'settled_at',
			'proceed_at',
			'completed_at',
			'saran',
			'hasil',
			'b.full_name as bendahara_name',
			'pptk.full_name as pptk_name',
			'pttd.full_name as pttd_name',
			'pel.full_name as pelaksana_name',
			'ang.nama_rekening as anggaran_name',
			DB::raw("case when to_char(tgl_kembali, 'YYYY-MM-DD') <= to_char(now(), 'YYYY-MM-DD') and completed_at is null and proceed_at is not null then 1 else 0 end as can_finish"),
			DB::raw("case when settled_at is null and completed_at is not null then 1 else 0 end as can_generate")
		)->first();
		
		$child = [];
		if ($header != null){
			$biaya = DB::table('biaya')->whereNull('deleted_at')
			->select('pegawai_id', 'spt_id', 'total_biaya');

			$child = SPTDetail::join('pegawai as p', 'p.id', 'spt_detail.pegawai_id')
			->leftJoinSub($biaya, 'biaya', function ($join) {
					$join->on('spt_detail.spt_id', 'biaya.spt_id')
					->on('spt_detail.pegawai_id', 'biaya.pegawai_id');
			})->where('spt_detail.spt_id', $id)
			->select(
				'spt_detail.id',
				'sppd_file_id',
				'p.id as pegawai_id',
				'full_name',
				'nip',
				DB::raw("coalesce(total_biaya,0) as total_biaya"),
				DB::raw("case when 1 = {$isAdmin} or spt_detail.created_by = {$loginid} then true else false end as can_edit")
			)->orderBy('is_pelaksana', 'DESC')
			->orderBy('nip')
			->orderBy('full_name')
			->get();
		}

		$results['data']  = array( 'header' => $header, 'child' => $child);

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function getSPPD($id, $pegawaiId)
	{
		$results = $this->responses;
		$data = SPTDetail::join('files as f', 'f.id', 'sppd_file_id')
		->where('spt_detail.id', $id)
		->where('pegawai_id', $pegawaiId)
		->first();

		if($data != null){
			$results['data'] = $data->file_path . $data->file_name;
		} else {
			array_push($results['message'], 'SPPD tidak ditemukan!');
		}
		return response()->json($results, $results['state_code']);
	}

  public function show(Request $request, $id, $sptDetailId, $pegawaiId)
  {
		$results = $this->responses;

		$user = $request->user();
		$isAdmin = $user->tokenCan('is_admin') ? 1 : 0;
		$loginId = $user->id;

		$results['data'] = SPT::join('spt_detail as sd', 'sd.spt_id', 'spt.id')
		->join('pegawai as b', 'b.id', 'bendahara_id')
		->join('pegawai as pptk', 'pptk.id', 'pptk_id')
		->join('pegawai as pttd', 'pttd.id', 'pttd_id')
		->join('pegawai as pel', 'pel.id', 'pelaksana_id')
		->join('anggaran as ang', 'ang.id', 'anggaran_id')
		->where('sd.id', $sptDetailId)
		->select(
			'no_spt',
			'tgl_berangkat',
			'tgl_kembali',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tglb_text"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tglk_text"),
			'daerah_asal',
			'daerah_tujuan',
			'transportasi',
			'spt.settled_at',
			'b.full_name as bendahara_name',
			'pptk.full_name as pptk_name',
			'pttd.full_name as pttd_name',
			'pel.full_name as pelaksana_name',
			'ang.nama_rekening as anggaran_name',
			DB::raw("( select full_name from pegawai where id = {$pegawaiId} and deleted_at is null limit 1 ) as pegawai_text"),
			DB::raw("( select id from biaya where spt_id = {$id} and pegawai_id = {$pegawaiId} and deleted_at is null limit 1 ) as biaya_id"),
			DB::raw("( select total_biaya from biaya where spt_id = {$id} and pegawai_id = {$pegawaiId} and deleted_at is null limit 1 ) as total_biaya"),
			DB::raw("( select sppd_file_id from spt_detail as sd where spt.id = spt_id and deleted_at is null and pegawai_id = {$pegawaiId} limit 1 ) as sppd_file_id"),
			DB::raw(" case when spt.created_by = {$loginId} or {$isAdmin} = 1 then 1 else 0 end as can_kwitansi"),
			
		)->first();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
  }

	public function cetakSPPD($id)
	{
		$results = $this->responses;
		
		$sppd = SPTDetail::find($id);
		if($sppd->sppd_file_id != null) {
			$file = DB::table('files')->where('id', $sppd->sppd_file_id)->first();
			$results['data'] = $file->file_path . $file->file_name;
			$results['success'] = true;
			$results['state_code'] = 200;
		}
		return response()->json($results, $results['state_code']);
		
		return response()->json($results, $results['state_code']);
	}

	public function cetakRumming($id, $biayaId, $pegawaiId)
	{
		$results = $this->responses;
		$sptDetail = SPTDetail::where('spt_id', $id)->where('pegawai_id', $pegawaiId)->first();
		if($sptDetail->rumming_file_id == null) {
			$templatePath = base_path('public/storage/template/template_rumming.docx');
			$checkFile = FaFile::exists($templatePath);
			if($checkFile) {
				$pegawai = Pegawai::where('id', $pegawaiId)
				->select(DB::raw("coalesce(nip,'-') as nip"), 'full_name as pegawai_name')
				->first();
	
				$kadin = Pegawai::where('pegawai.id', 5)
				->select('nip', 'full_name as kadin_name')
				->first();
	
				$spt = SPT::join('anggaran as a', 'a.id', 'anggaran_id')
				->where('spt.id', $id)
				->select(
					'tgl_spt',
					'no_spt',
					'jumlah_hari',
					'daerah_tujuan',
					'no_index',
					'bendahara_id'
				)->first();

				$bendahara = DB::table('pegawai as p')
				->where('id', $spt->bendahara_id)
				->select('nip', 'full_name as bendahara_name')
				->first();
				
				// $bendahara = DB::table('pejabat_ttd as pt')
				// ->join('pegawai as p', 'p.id', 'pt.pegawai_id')
				// ->where('autorisasi', 'Bendahara')
				// ->where('is_active', '1')
				// ->select('nip', 'full_name as bendahara_name')
				// ->first();
				
				$nameFile = "090_".$spt->no_index."_SPPD_PDK_2021_".$pegawai->nip;
	
				try {
					$biayaTb = array();
					$pengeluaran = Pengeluaran::where('biaya_id', $biayaId)
					->where('pegawai_id', $pegawaiId)
					->groupBy('kategori')
					->select(
						'kategori as pengeluaran',
						DB::raw("sum(jml) as qty"),
						DB::raw("sum(nominal) as harga"),
						DB::raw("string_agg(catatan, ', ') as catatan")
					)->get();
					
					if(count($pengeluaran) > 0) {
							foreach($pengeluaran as $p) {
								array_push($biayaTb, $this->mapBiaya($p));
							}
					}
		
					$transport = Transport::where('biaya_id', $biayaId)
					->where('pegawai_id', $pegawaiId)
					->groupBy('jenis_transport')
					->select(
						'jenis_transport as pengeluaran',
						DB::raw("sum(1) as qty"),
						DB::raw("sum(total_bayar) as harga"),
						DB::raw("string_agg(catatan, ', ') as catatan")
					)->get();
					if(count($transport) > 0) {
						foreach($transport as $t) {
							array_push($biayaTb, $this->mapBiaya($t));
						}
					}
		
					$inap = Inap::where('biaya_id', $biayaId)
					->where('pegawai_id', $pegawaiId)
					->select(
						DB::raw("'Penginapan ' || hotel as pengeluaran"),
						'jml_hari as qty',
						'harga',
						'catatan'
					)->get();

					if(count($inap) > 0) {
						foreach($inap as $i) {
							array_push($biayaTb, $this->mapBiaya($i));
						}
					}
	
					$grandTotal = array_sum(array_map(function ($val){ return $val->totalRaw; },$biayaTb));
					$terbilang = Utils::rupiahTeks($grandTotal);
					$tgl = (new Carbon($spt->tgl_spt))->isoFormat('D MMMM Y');
		
					$template = new TemplateProcessor($templatePath);
					
					$template->setValue('jumlah', number_format($grandTotal));
					$template->setValue('terbilang', $terbilang);
					$template->setValue('no_spt', $spt->no_spt);
					$template->setValue('tgl', $tgl);
					$template->setValue('jml_hari', $spt->jumlah_hari);
					$template->setValue('daerah_tujuan', $spt->daerah_tujuan);
					$template->setValue('nama_bendahara', $bendahara->bendahara_name);
					$template->setValue('nip_bendahara', $bendahara->nip);
					$template->setValue('nama_penerima', $pegawai->pegawai_name);
					$template->setValue('nip_penerima', $pegawai->nip);
					$template->setValue('nama_kadin', $kadin->kadin_name);
					$template->setValue('nip_kadin', $kadin->nip);
					$template->cloneRowAndSetValues('pengeluaran', $biayaTb);
					
					$newFile = new \stdClass();
					$newFile->dbPath ='/storage/rumming/';
					$newFile->ext = '.pdf';
					$newFile->originalName = "rumming_" . $nameFile;
					$newFile->newName = $newFile->originalName;

					$path = base_path('/public');
					$docPath = $path . $newFile->dbPath. $newFile->originalName . ".docx";
					$template->saveAs($docPath, TRUE);
					
					$converter = new OfficeConverter($docPath);
					//generates pdf file in same directory as test-file.docx
					$converter->convertTo($newFile->originalName .".pdf");
					if(FaFile::exists($docPath)) {
						FaFile::delete($docPath);
					}
					
					//upload to table
					$file = Utils::saveFile($newFile);

					$loginId = auth('sanctum')->user()->id;
					$sptDetail->update([
						'rumming_file_id' => $file
					]);

					//save to log
					SPTLog::create([
						'user_id' => auth('sanctum')->user()->id,
						'username' => auth('sanctum')->user()->pegawai->full_name,
						'reference_id' => $id,
						'aksi' => 'Cetak Rumming ' . $pegawai->pegawai_name,
						'success' => '1'
					]);
	
					$results['success'] = true;
					$results['state_code'] = 200;
					$results['data'] = $newFile->dbPath . $newFile->originalName . ".pdf";
				} catch (\Exception $e) {
					Log::channel('spderr')->info('sppd_rumming: '. json_encode($e->getMessage()));
					array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
				}
	
			} else {
				array_push($results['messages'], 'Kesalahan! Template tidak ditemukan.');
			}
		} else {
			$file = DB::table('files')->where('id', $sptDetail->rumming_file_id)->first();
			$results['data'] = $file->file_path . $file->file_name . ".pdf";
			$results['success'] = true;
			$results['state_code'] = 200;
		}

		return response()->json($results, $results['state_code']);
	}

	public function cetakLaporan(Request $request, $id)
	{
		$results = $this->responses;
		$spt = SPT::find($id)->update([
			'hasil' => $request->hasil ?? '',
			'saran' => $request->saran ?? ''
		]);

		$updateSpt = SPT::find($id);
		if(true) { 
			$templatePath = base_path('public/storage/template/template_laporan.docx');
			$checkFile = FaFile::exists($templatePath);
			if($checkFile) {
				$pelaksana = DB::table('pegawai as p')
				->where('id', $updateSpt->pelaksana_id)
				->select(
					'nip', 
					'full_name as nama',
					'jabatan',
					DB::raw("pangkat || ' ' || golongan as pangkat"))
				->first();

				$nameFile = "laporan_090_".$updateSpt->index."_SPPD_PDK_2021";

				$tgl_cetak = (new Carbon())->isoFormat('D MMMM Y');
				$tglAwal = (new Carbon($updateSpt->tgl_berangkat))->isoFormat('D MMMM Y');
				$tglAkhir = (new Carbon($updateSpt->tgl_kembali))->isoFormat('D MMMM Y');

				$users = SPTDetail::join('pegawai as p', 'p.id', 'spt_detail.pegawai_id')
				->where('spt_id',$id)
				->select(
					'is_pelaksana',
					'full_name as nama_pegawai')
				->orderBy('is_pelaksana', 'DESC')
				->orderBy('full_name')
				->get();
				$tempUserValue = array();
				$tempPengikut = array();
				$countPengikut = 1;
				foreach($users as $key => $user){
					if ($user->is_pelaksana == false) {
						$temp1 = array(
							'np' => $countPengikut,
							'nama_pengikut' => $user->nama_pegawai
						);
						array_push($tempPengikut, $temp1);
						$countPengikut++;
					}
					$temp = array(
						'np' => $key + 1,
						'nama_pengikut' => $user->nama_pegawai
					);
					array_push($tempUserValue, $temp);
				}
				
				//
				$template = new TemplateProcessor($templatePath);
				
				$template->setValue('tgl_cetak', $tgl_cetak);
				$template->setValue('nama_pelaksana', $pelaksana->nama);
				$template->setValue('nip_pelaksana', $pelaksana->nip);
				$template->setValue('pangkat_pelaksana', $pelaksana->pangkat);
				$template->setValue('jabatan_pelaksana', $pelaksana->jabatan);
				$template->setValue('no_spt', $updateSpt->no_spt);
				$template->setValue('tgl_dinas', $tglAwal . ' s.d ' . $tglAkhir);
				$template->setValue('tujuan', $updateSpt->untuk);
				$template->setValue('maksud', $updateSpt->dasar_pelaksana);
				$template->setValue('saran', strip_tags($updateSpt->saran));

				$tHasil = array();
				$hasil = explode("<li>",$updateSpt->hasil);
				$ctr = 1;
				foreach($hasil as $hsl) {
					$vHsl = strip_tags($hsl);
					if(!empty($vHsl)){
						$tmpHsl = array (
							'nh' => $ctr,
							'hasil' => strip_tags($hsl)
						);
						array_push($tHasil, $tmpHsl);
						$ctr++;
					}
				}

				$template->cloneRowAndSetValues('np', $tempPengikut);
				$template->cloneRowAndSetValues('nh', $tHasil);
				$template->cloneRowAndSetValues('np', $tempUserValue);

				$newFile = new \stdClass();
				$newFile->dbPath ='/storage/laporan/';
				$newFile->ext = '.pdf';
				$newFile->originalName = $nameFile;
				$newFile->newName = $newFile->originalName;
				
				$path = base_path('/public');
				$template->saveAs($path . $newFile->dbPath . $newFile->newName . ".docx", TRUE);
				
				$docPath = $path . $newFile->dbPath . $newFile->newName . ".docx";
				$converter = new OfficeConverter($docPath);
				$converter->convertTo($newFile->newName.".pdf");

				if(FaFile::exists($path . $newFile->dbPath . $newFile->newName . ".docx")) {
					FaFile::delete($path . $newFile->dbPath . $newFile->newName . ".docx");
				}
				
				$results['success'] = true;
				$results['state_code'] = 200;
				array_push($results['messages'], 'OK.');
				$results['data'] = $newFile->dbPath . $newFile->newName . ".pdf";

			} else {
				array_push($results['messages'], 'Template Laporan SPT tidak ditemukan.');
			}
		} else {
			$file = DB::table('files')->where('id', $updateSpt->laporan_file_id)->first();
			$results['data'] = $file->file_path . $file->file_name. ".pdf";
			$results['success'] = true;
			$results['state_code'] = 200;
		}
		
		return response()->json($results, $results['state_code']);
	}

	public function cetakKwitansi($id)
	{
		$results = $this->responses;
		$updateSpt = SPT::find($id);
		if($updateSpt->kwitansi_file_id == null) {
			$templatePath = base_path('public/storage/template/template_kwitansi.docx');
			$checkFile = FaFile::exists($templatePath);
			if($checkFile) {
				$spt = SPT::join('anggaran as a', 'a.id', 'anggaran_id')
				->join('pegawai as p', 'p.id', 'spt.pelaksana_id')
				->where('spt.id', $id)
				->select(
					'kode_rekening',
					'nama_rekening',
					'spt.periode',
					'dasar_pelaksana',
					'daerah_asal',
					'daerah_tujuan',
					'tgl_berangkat',
					'tgl_kembali',
					'untuk',
					'tgl_spt',
					'no_spt',
					'p.full_name as nama_penyelenggara',
					'p.nip as nip_pengelenggara',
					'no_index',
					'pptk_id',
					'bendahara_id'
				)->first();
	
				$nameFile = "090_".$spt->index."_SPPD_PDK_2021";
				$totalBiaya = Biaya::where('spt_id', $id)->sum('total_biaya');
				
				$bendahara = DB::table('pegawai as p')
				->where('id', $spt->bendahara_id)
				->select('nip', 'full_name')
				->first();
				
				$ppk = DB::table('pegawai as p')
				->where('id', $spt->pptk_id)
				->select('nip', 'full_name')
				->first();

				$kadin = Pegawai::where('pegawai.id', 5)
				->select('nip', 'full_name')
				->first();

				$totalBiaya = Biaya::where('spt_id', $id)->sum('total_biaya');
				$terbilang = Utils::rupiahTeks($totalBiaya);
				$tgl = (new Carbon($spt->tgl_spt))->isoFormat('D MMMM Y');
				
				$template = new TemplateProcessor($templatePath);

				$template->setValue('tahun_anggaran', $spt->periode);
				$template->setValue('kode_rekening', $spt->kode_rekening);
				$template->setValue('nama_rekening', $spt->nama_rekening);
				$template->setValue('nama_bendahara', $bendahara->full_name);
				$template->setValue('nip_bendahara', $bendahara->nip);
				$template->setValue('total_biaya', number_format($totalBiaya));
				$template->setValue('terbilang', $terbilang);
				$template->setValue('maksud', $spt->dasar_pelaksana);
				$template->setValue('no_spt', $spt->no_spt);
				$template->setValue('tgl_spt', $tgl);

				$template->setValue('nama_kadin', $kadin->full_name);
				$template->setValue('nip_kadin', $kadin->nip);

				$template->setValue('nama_pptk', $ppk->full_name);
				$template->setValue('nip_pptk', $ppk->nip);
				
				$template->setValue('nama_penerima', $spt->nama_penyelenggara);
				$template->setValue('nip_penerima', $spt->nip_pengelenggara);
				
				$newFile = new \stdClass();
				$newFile->dbPath ='/storage/kwitansi/';
				$newFile->ext = '.pdf';
				$newFile->originalName = "kwitansi_" . $nameFile;
				$newFile->newName = $newFile->originalName;
				
				$path = base_path('/public');
				$template->saveAs($path . $newFile->dbPath . $newFile->newName . ".docx", TRUE);
				
				$docPath = $path . $newFile->dbPath . $newFile->newName . ".docx";
				$converter = new OfficeConverter($docPath);
				$converter->convertTo($newFile->newName.".pdf");

				if(FaFile::exists($path . $newFile->dbPath . $newFile->newName . ".docx")) {
					FaFile::delete($path . $newFile->dbPath . $newFile->newName . ".docx");
				}
					
				try{
					// Start Transaction
					DB::beginTransaction();
					
					//upload to table
					$file = Utils::saveFile($newFile);

					//save to report
					$this->saveReport($id, $spt);

					$loginId = auth('sanctum')->user()->id;
					$updateSpt->update([
						'settled_at' => DB::raw("now()"),
						'settled_by' => $loginId,
						'kwitansi_file_id' => $file
					]);

					$updateSPPD = SPTDetail::where('spt_id', $id)
					->update([
						'settled_at' => now()->toDateTimeString(),
						'settled_by' => $loginId
					]);

					//save to log
					SPTLog::create([
						'user_id' => auth('sanctum')->user()->id,
						'username' => auth('sanctum')->user()->pegawai->full_name,
						'reference_id' => $id,						
						'aksi' => 'Cetak Kwitansi',
						'success' => '1'
					]);
					
					DB::commit();
					$results['success'] = true;
					$results['state_code'] = 200;
					$results['data'] = $newFile->dbPath . $newFile->newName . ".pdf";
				} catch (\Exception $e) {
					DB::rollBack();
					Log::channel('spderr')->info('spt_cetak_err: '. json_encode($e->getMessage()));
					array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
				}
	
			} else {
				array_push($results['messages'], 'Template SPT tidak ditemukan.');
			}
		} else {
			$file = DB::table('files')->where('id', $updateSpt->kwitansi_file_id)->first();
			$results['data'] = $file->file_path . $file->file_name. ".pdf";
			$results['success'] = true;
			$results['state_code'] = 200;
		}
		
		return response()->json($results, $results['state_code']);
	}

	private function saveReport($id, $spt)
	{
		$sppd = SPTDetail::where('spt_id', $id)->get();
		foreach($sppd as $dtl) {
			$biaya = Biaya::where('spt_id', $id)
			->where('pegawai_id', $dtl->pegawai_id)->first();

			$userJbtn = Pegawai::where('pegawai.id', $dtl->pegawai_id)
			->select(
				'full_name',
				'jabatan'
			)->first();

			$inap = Inap::where('biaya_id', $biaya->id)
			->where('pegawai_id', $dtl->pegawai_id)->first();

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

			$asal = ucwords(strtolower($spt->daerah_asal));
			$tujuan = ucwords(strtolower($spt->daerah_tujuan));
			$checkin = $inap->tgl_checkin ?? null;
			$checkout = $inap->tgl_checkout ?? null;

			$pesbrgkt_tgl = $pesawatBrgkt->tgl ?? null;
			$peskmbl_tgl = $pesawatPlg->tgl ?? null;

			$report = ReportSPPD::insert([
				'pegawai_id' => $dtl->pegawai_id,
				'spt_id' => $id,
				'spt_detail_id' => $dtl->id,
				'biaya_id' => $biaya->id,
				'nama_pelaksana' => $userJbtn->full_name,
				'jabatan' => $userJbtn->jabatan,
				'no_pku' => null,
				'no_spt' => $spt->no_spt,
				'no_sppd' => null,
				'kegiatan' => $spt->untuk,
				'penyelenggara' => 'SD Dalam Kab. Kerinci',
				'lok_asal'=> $asal,
				'lok_tujuan' => $tujuan,
				'tgl_berangkat' => $spt->tgl_berangkat,
				'tgl_kembali' => $spt->tgl_kembali,
				'uang_saku' => $uangSaku ?? null,
				'uang_makan' => $uangMakan ?? null,
				'uang_representasi' => $uangRepresentasi ?? null,
				'uang_penginapan'  => $biaya->total_biaya_inap ?? null,
				'uang_travel' => $biaya->total_biaya_travel ?? null,
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

	public function mapBiaya($db)
	{
		$ui = new \stdClass();
		$ui->pengeluaran = isset($db->pengeluaran) ? $db->pengeluaran : null;
		$ui->j = isset($db->qty) ? $db->qty : null;
		$ui->biaya = isset($db->harga) ? number_format($db->harga) : null;
		$ui->total = isset($db->harga) && isset($db->qty) ? number_format($db->harga * $db->qty) : null;
		$ui->totalRaw = isset($db->harga) && isset($db->qty) ? $db->harga * $db->qty : null;
		$ui->cttn = isset($db->catatan) ? $db->catatan : null;

		return $ui;
	}

	function mapSPT($db){
		$ui = new \stdClass();
		$ui->id = isset($db->id) ? $db->id : "";
		$ui->jml_hari = isset($db->jumlah_hari) ? $db->jumlah_hari : "";
		$ui->tgl_berangkat = isset($db->tgl_berangkat) ? (new Carbon($db->tgl_berangkat))->isoFormat('D MMMM Y') : "";
		$ui->tgl_kembali = isset($db->tgl_kembali) ? (new Carbon($db->tgl_kembali))->isoFormat('D MMMM Y') : "";
		$ui->tgl_spt = isset($db->tgl_spt) ? (new Carbon($db->tgl_spt))->isoFormat('D MMMM Y') : "";

		$ui->daerah_asal = ucwords(strtolower($db->daerah_asal));
		$ui->daerah_tujuan = ucwords(strtolower($db->daerah_tujuan));
		$ui->no_spt = isset($db->no_spt) ? $db->no_spt : "";
		$ui->periode = isset($db->periode) ? $db->periode : "";
		$ui->no_index = isset($db->no_index) ? $db->no_index : "";
		$ui->untuk = isset($db->untuk) ? $db->untuk : "";
		$ui->transportasi = isset($db->transportasi) ? $db->transportasi : "";
		$ui->dasar_pelaksana = isset($db->dasar_pelaksana) ? $db->dasar_pelaksana : "";

		return $ui;
	}
}
