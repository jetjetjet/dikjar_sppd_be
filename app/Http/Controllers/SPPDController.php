<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\User;
use App\Models\Transport;
use App\Models\Inap;
use App\Models\Biaya;
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
  
	public function grid($id)
	{
		$results = $this->responses;

		$header = SPT::where('id', $id)
		->select(
			'no_spt',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tgl_kembali"),
			DB::raw("coalesce(kota_tujuan, kec_tujuan) as kota_tujuan"),
			DB::raw("coalesce(kota_asal, kec_asal) as kota_asal"),
			'transportasi'
		)->first();
		
		$child = [];
		if ($header != null){
			$child = SPTDetail::join('users as u', 'u.id', 'spt_detail.user_id')
			->where('spt_id', $id)
			->select(
				'spt_detail.id',
				'sppd_file_id',
				'u.id as user_id',
				'full_name',
				'nip'
			)->get();
		}

		$results['data']  = array( 'header' => $header, 'child' => $child);

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function getSPPD($id, $userId)
	{
		$results = $this->responses;
		$data = SPTDetail::join('files as f', 'f.id', 'sppd_file_id')
		->where('spt_detail.id', $id)
		->where('user_id', $userId)
		->first();

		if($data != null){
			$results['data'] = $data->file_path . $data->file_name;
		} else {
			array_push($results['message'], 'SPPD tidak ditemukan!');
		}
		return response()->json($results, $results['state_code']);
	}

	public function cetakSPPD($id, $sptDetailId, $userId)
	{
		$results = $this->responses;
		$spt = SPT::find($id);
		
		if(1 == 1){
			$templatePath = base_path('public/storage/template/template_sppd.docx');
			$checkFile = FaFile::exists($templatePath);
			if($checkFile){
				$user = User::join('jabatan as j', 'j.id', 'users.jabatan_id')
				->where('users.id',$userId)
				->select(
					'full_name as nama_pegawai', 
					'j.name as jabatan_pegawai', 
					'nip as nip_pegawai')
				->first();

				$spt = SPT::find($id);
				try{
					
					$brgkt = new Carbon($spt->tgl_berangkat);
					$kembali = new Carbon($spt->tgl_kembali);
					$brgktPlus = $brgkt->addDay();
					$kembaliMinus = $kembali->subDay(); 
					$tglSppd = Carbon::now()->isoFormat('D MMMM Y');
					$jmlHari = $brgkt->diff($kembali)->days;

					$asal = $spt->kota_asal != null ? ucwords(strtolower($spt->kota_asal)) : ucwords(strtolower($spt->kec_asal));
					$tujuan = $spt->kota_tujuan != null ? ucwords(strtolower($spt->kota_tujuan)) : ucwords(strtolower($spt->kec_tujuan));

					$template = new TemplateProcessor($templatePath);
	
					// $template->setValue('dasar_pelaksana', $spt->dasar_pelaksana);
					$template->setValue('nama_pegawai', $user->nama_pegawai);
					$template->setValue('untuk', $spt->untuk);
					$template->setValue('transportasi', $spt->transportasi);
					$template->setValue('jml_hari', $jmlHari . " Hari");
					$template->setValue('daerah_asal', $asal);
					$template->setValue('daerah_tujuan', $tujuan);
					$template->setValue('tgl_berangkat', $brgkt->isoFormat('D MMMM Y'));
					$template->setValue('tgl_kembali', $kembali->isoFormat('D MMMM Y'));
					$template->setValue('tgl_berangkat_plus', $brgktPlus->isoFormat('D MMMM Y'));
					$template->setValue('tgl_kembali_minus', $kembaliMinus->isoFormat('D MMMM Y'));
					$template->setValue('tgl_sppd', $tglSppd);
					$template->setValue('no_spt', $spt->no_spt);
					
					$newFile = new \stdClass();
					$newFile->dbPath ='storage/spt/';
					$newFile->ext = '.pdf';
					$newFile->originalName = "SPPD_Generated";
					$newFile->newName = time()."_".$newFile->originalName;

					$template->saveAs(base_path('public/' . $newFile->dbPath . $newFile->newName . ".docx"), TRUE);
					//Convert kwe PDF
					$docPath = base_path('public/' . $newFile->dbPath . $newFile->newName . ".docx");
          $converter = new OfficeConverter($docPath);
          //generates pdf file in same directory as test-file.docx
          $converter->convertTo($newFile->newName.".pdf");

					$newFile->newName = $newFile->newName.".pdf";
					$file = Utils::saveFile($newFile);
					// update
					SPTDetail::where('id', $sptDetailId)
					->where('user_id', $userId)
					->update([
						'sppd_file_id' => $file
					]);
					
					array_push($results['messages'], 'Berhasil membuat SPPD.');
					$results['success'] = true;
					$results['state_code'] = 200;
				} catch (\Exception $e) {
					Log::channel('spderr')->info('sppd_cetak: '. json_encode($e->getMessage()));
					array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
				}
	
			} else {
				array_push($results['messages'], 'Template SPPD tidak ditemukan.');
			}
		} else {
			array_push($results['messages'], 'Berkas SPPD sudah pernah dibuat.');
		}
		
		return response()->json($results, $results['state_code']);
	}

  public function show($id, $sptDetailId, $userId)
  {
		$results = $this->responses;

		$header = SPT::where('id', $id)
		->select(
			'no_spt',
			'tgl_berangkat',
			'tgl_kembali',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tglb_text"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tglk_text"),
			DB::raw("coalesce(kota_tujuan, kec_tujuan) as kota_tujuan"),
			DB::raw("coalesce(kota_asal, kec_asal) as kota_asal"),
			'transportasi'
		)->first();

		$child = null;
		if ($header != null){
			$check = Biaya::where('spt_id', $id)
			->where('user_id', $userId)
			->select(
				'id',
				'uang_makan',
				'uang_saku',
				'uang_representasi',
				'uang_inap',
				'uang_travel',
				'uang_pesawat',
				'jml_biaya',
			)->first();

			if($check != null){
				$child = $check;
			}
		}
		
		$results['data']  = array( 'header' => $header, 'child' => $child);
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
  }
}
