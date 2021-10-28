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

		$header = SPT::where('id', $id)
		->select(
			'no_spt',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tgl_kembali"),
			'daerah_asal',
			'daerah_tujuan',
			'transportasi',
			'finished_at',
			'proceed_at'
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
				DB::raw("case when 1 = {$isAdmin} or p.id = {$loginid} then true else false end as can_edit")
			)->get();
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
		$loginid = $user->id;

		$results['data'] = SPT::where('id', $id)
		->select(
			'no_spt',
			'tgl_berangkat',
			'tgl_kembali',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tglb_text"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tglk_text"),
			'daerah_asal',
			'daerah_tujuan',
			'transportasi',
			'finished_at',
			DB::raw("( select full_name from pegawai where id = {$pegawaiId} and deleted_at is null limit 1 ) as pegawai_text"),
			DB::raw("( select id from biaya where spt_id = {$id} and pegawai_id = {$pegawaiId} and deleted_at is null limit 1 ) as biaya_id"),
			DB::raw("( select total_biaya from biaya where spt_id = {$id} and pegawai_id = {$pegawaiId} and deleted_at is null limit 1 ) as total_biaya"),
			DB::raw("( select sppd_file_id from spt_detail as sd where spt.id = spt_id and deleted_at is null and pegawai_id = {$pegawaiId} limit 1 ) as sppd_file_id")
		)->first();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
  }

	public function cetakSPPD($id, $pegawaiId)
	{
		$results = $this->responses;
		try{
			$templateSppdPath = base_path('public/storage/template/template_sppd.docx');
			$sppdFile = FaFile::exists($templateSppdPath);
			
			if(!$sppdFile) {
				throw new \Exception('Template SPPD tidak ditemukan');
			}

			$spt = SPT::find($id);
			$sptData = $this->mapSPT($spt);
			$user = SPTDetail::join('pegawai as p', 'p.id', 'spt_detail.pegawai_id')
			->join('jabatan as j', 'j.id', 'p.jabatan_id')
			->where('spt_id',$id)
			->where('pegawai_id', $pegawaiId)
			->select(
				'full_name as nama_pegawai', 
				'j.name as jabatan_pegawai', 
				'j.golongan as golongan_pegawai',
				'spt_detail.id',
				'pegawai_id',
				'nip as nip_pegawai')
			->first();
			
			$tempSppd = new TemplateProcessor($templateSppdPath);

			// $template->setValue('dasar_pelaksana', $spt->dasar_pelaksana);
			$tempSppd->setValue('nama_pegawai', $user->nama_pegawai);
			$tempSppd->setValue('jabatan_pegawai', $user->jabatan_pegawai);
			$tempSppd->setValue('golongan_pegawai', $user->golongan_pegawai);
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

			$newFile = new \stdClass();
			$newFile->dbPath ='/storage/spt/';
			$newFile->ext = '.pdf';
			$newFile->originalName = "SPPD_" . $user->nama_pegawai;
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
			$results['data'] = $newFile->dbPath . $newFile->newName;
		} catch (\Exception $e) {
			Log::channel('spderr')->info('sppd_cetak: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}
		
		return response()->json($results, $results['state_code']);
	}

	public function cetakRumming($id, $biayaId, $pegawaiId)
	{
		$results = $this->responses;
		$templatePath = base_path('public/storage/template/template_rumming.docx');
		$checkFile = FaFile::exists($templatePath);
		if($checkFile) {
			$pegawai = Pegawai::join('jabatan as j', 'j.id', 'jabatan_id')
			->where('pegawai.id', $pegawaiId)
			->select('nip', 'full_name')
			->first();
			
			$bendahara = DB::table('pejabat_ttd as pt')
			->join('pegawai as p', 'p.id', 'pt.pegawai_id')
			->where('autorisasi', 'Bendahara')
			->where('is_active', '1')
			->select('nip', 'full_name')
			->first();

			$kadin = Pegawai::join('jabatan as j', 'j.id', 'jabatan_id')
			->where('pegawai.id', 5)
			->select('nip', 'full_name')
			->first();

			$spt = SPT::join('anggaran as a', 'a.id', 'anggaran_id')
			->where('spt.id', $id)
			->select(
				'tgl_spt',
				'no_spt',
				'jumlah_hari',
				'daerah_tujuan',
				'no_index'
			)->first();
			
			$nameFile = "090_".$spt->no_index."_SPPD_PDK_2021_".$pegawai->nip;
			
			$oldFile = base_path('public/storage/rumming/'. $nameFile . '.pdf');
			if(FaFile::exists($oldFile)) {
				FaFile::delete($oldFile);
			}

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
				
				foreach($pengeluaran as $p) {
					array_push($biayaTb, $this->mapBiaya($p));
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
				
				foreach($transport as $t) {
					array_push($biayaTb, $this->mapBiaya($t));
				}
	
				$inap = Inap::where('biaya_id', $biayaId)
				->where('pegawai_id', $pegawaiId)
				->select(
					DB::raw("'Penginapan ' || hotel as pengeluaran"),
					'jml_hari as qty',
					'harga',
					'catatan'
				)->get();
				
				foreach($inap as $i) {
					array_push($biayaTb, $this->mapBiaya($i));
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
				$template->setValue('daerah_tujuan', ucwords(strtolower($spt->daerah_tujuan)));
				$template->setValue('nama_bendahara', $bendahara->full_name);
				$template->setValue('nip_bendahara', $bendahara->nip);
				$template->setValue('nama_penerima', $pegawai->full_name);
				$template->setValue('nip_penerima', $pegawai->nip);
				$template->setValue('nama_kadin', $kadin->full_name);
				$template->setValue('nip_kadin', $kadin->nip);
				$template->cloneRowAndSetValues('pengeluaran', $biayaTb);
				
				$path = base_path('/public');
				$docPath = $path . '/storage/rumming/'. $nameFile . ".docx";
				$template->saveAs($docPath, TRUE);
				
				$converter = new OfficeConverter($docPath);
				//generates pdf file in same directory as test-file.docx
				$converter->convertTo($nameFile.".pdf");
				if(FaFile::exists($docPath)) {
					FaFile::delete($docPath);
				}

				$results['success'] = true;
				$results['state_code'] = 200;
				$results['data'] = '/storage/rumming/'. $nameFile . ".pdf";
			} catch (\Exception $e) {
				Log::channel('spderr')->info('sppd_rumming: '. json_encode($e->getMessage()));
				array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
			}

		} else {
			array_push($results['messages'], 'Kesalahan! Template tidak ditemukan.');
		}

		return response()->json($results, $results['state_code']);
	}

	public function cetakKwitansi($id)
	{
		$results = $this->responses;
		// $spt = SPT::find($id);

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
				'untuk',
				'tgl_spt',
				'no_spt',
				'p.full_name as nama_penyelenggara',
				'p.nip as nip_pengelenggara',
				'no_index',
				'pptk_id'
			)->first();

			$nameFile = "090_".$spt->index."_SPPD_PDK_2021";
			$totalBiaya = Biaya::where('spt_id', $id)->sum('total_biaya');
			
			$oldFile = base_path('public/storage/kwitansi/kwitansi_'. $nameFile . '.pdf');
			if(FaFile::exists($oldFile)) {
				FaFile::delete($oldFile);
			}

			try{
				$bendahara = DB::table('pejabat_ttd as pt')
				->join('pegawai as p', 'p.id', 'pt.pegawai_id')
				->where('autorisasi', 'Bendahara')
				->where('is_active', '1')
				->select('nip', 'full_name')
				->first();
				
				$ppk = DB::table('pegawai as p')
				->where('id', $spt->pptk_id)
				->select('nip', 'full_name')
				->first();

				$kadin = Pegawai::join('jabatan as j', 'j.id', 'jabatan_id')
				->where('pegawai.id', 5)
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
				
				$results['success'] = true;
				$results['state_code'] = 200;
				$results['data'] = $newFile->dbPath . $newFile->newName . ".pdf";
			} catch (\Exception $e) {
				Log::channel('spderr')->info('spt_cetak_err: '. json_encode($e->getMessage()));
				array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
			}

		} else {
			array_push($results['messages'], 'Template SPT tidak ditemukan.');
		}
		
		return response()->json($results, $results['state_code']);
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
