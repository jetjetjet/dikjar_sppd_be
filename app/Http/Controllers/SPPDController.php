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
			'finished_at'
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
			DB::raw("( select full_name from pegawai where id = {$pegawaiId} and deleted_at is null ) as pegawai_text"),
			DB::raw("( select id from biaya where spt_id = {$id} and pegawai_id = {$pegawaiId} and deleted_at is null ) as biaya_id"),
			DB::raw("( select total_biaya from biaya where spt_id = {$id} and pegawai_id = {$pegawaiId} and deleted_at is null ) as total_biaya"),
			DB::raw("(select sppd_file_id from spt_detail as sd where spt.id = spt_id and deleted_at is null and pegawai_id = {$pegawaiId} ) as sppd_file_id")
		)->first();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
  }

	public function cetakRampung($id, $biayaId, $pegawaiId)
	{
		$results = $this->responses;
		$templatePath = base_path('public/storage/template/template_rampung.docx');
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
			
			$oldFile = base_path('public/storage/rampung/'. $nameFile . '.pdf');
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
				$template->cloneRowAndSetValues('pengeluaran', $biayaTb);
				
				$path = base_path('/public');
				$docPath = $path . '/storage/rampung/'. $nameFile . ".docx";
				$template->saveAs($docPath, TRUE);
				
				$converter = new OfficeConverter($docPath);
				//generates pdf file in same directory as test-file.docx
				$converter->convertTo($nameFile.".pdf");
				if(FaFile::exists($docPath)) {
					FaFile::delete($docPath);
				}

				$results['success'] = true;
				$results['state_code'] = 200;
				$results['data'] = '/storage/rampung/'. $nameFile . ".pdf";
			} catch (\Exception $e) {
				Log::channel('spderr')->info('sppd_rampung: '. json_encode($e->getMessage()));
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
				'no_index'
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

				$template->setValue('pengguna_anggaran', $spt->nama_penyelenggara);
				$template->setValue('nip_pengg_angg', $spt->nip_pengelenggara);

				// $template->setValue('nama_pptk', $pejabat->golongan);
				// $template->setValue('nip_pptk', $pejabat->golongan);
				
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
}
