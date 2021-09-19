<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\User; 
use App\Models\Jabatan;
use DB;
use Validator;
use Carbon\Carbon;

use Illuminate\Support\Facades\File as FaFile;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Helpers\Utils;

class SPTController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = SPT::select(
			'id',
			'no_spt',
			DB::raw("tgl_berangkat || ' s/d ' || tgl_kembali as tgl"),
			'kota_tujuan as tujuan',
			'untuk',
			DB::raw("'__' as nama")
		)->get();

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function cetakSPT($id)
	{
		$results = $this->responses;
		$spt = SPT::find($id);
		
		if($spt->status == 'DRAFT'){
			$templatePath = base_path('public/storage/template/template_spt.docx');
			$checkFile = FaFile::exists($templatePath);
			if($checkFile){
				$users = SPTDetail::join('users as u', 'u.id', 'spt_detail.user_id')
				->join('jabatan as j', 'j.id', 'u.jabatan_id')
				->where('spt_id',$id)
				->select(
					DB::raw("ROW_NUMBER() OVER (ORDER BY spt_detail.id) AS index_no"), 
					'full_name as nama_pegawai', 
					'j.name as jabatan_pegawai', 
					'nip as nip_pegawai')
				->get();
	
				$userValue = array();
				foreach($users as $user){
					$temp = array(
						'index_no' => $user->index_no,
						'nama_pegawai' => $user->nama_pegawai,
						'jabatan_pegawai' => $user->jabatan_pegawai,
						'nip_pegawai' => $user->nip_pegawai
					);
					array_push($userValue, $temp);
				}
	
				$kadin = User::role('KADIN')->first();
				// $jabatanKadin = Jabatan::find($kadin->jabatan_id)->first();
	
				try{
					$template = new TemplateProcessor($templatePath);
	
					$template->setValue('dasar_pelaksana', $spt->dasar_pelaksana);
					$template->setValue('untuk', $spt->untuk);
					$template->setValue('tgl_berangkat', $spt->tgl_berangkat);
					$template->setValue('tgl_kembali', $spt->tgl_kembali);
					$template->setValue('nomor_surat', $spt->no_spt);
					$template->setValue('nama_kadin', $kadin->full_name);
					$template->setValue('nip_kadin', $kadin->nip);
					// $template->setValue('jabatan_kadin', $jabatanKadin->name);
		
					$template->cloneRowAndSetValues('index_no', $userValue);
		
					$newPath = base_path('public/storage/template/');
					$newFileName = time()."_".$spt->no_spt.".docx";
					$newFile = new \stdClass();
					$newFile->dbPath ='public/storage/spt/';
					$newFile->ext = '.docx';
					$newFile->originalName = "SPT_Generated.docx";
					$newFile->newName = time()."_".$newFile->originalName;

					$template->saveAs(base_path($newFile->dbPath . $newFile->newName));

					$file = Utils::saveFile($newFile);

					//update
					$spt->update([
						'spt_file_id' => $file,
						'spt_generated_at' => date('Y-m-d H:i:s'),
						'spt_generated_by' => 1,
						'status' => 'SPTGENERATED'
					]);
					
					array_push($results['messages'], 'Berhasil membuat SPT.');
					$results['success'] = true;
					$results['state_code'] = 200;
				} catch (\Exception $e){
					array_push($results['messages'], $e->getMessage());
				}
	
			} else {
				array_push($results['messages'], 'Template SPT tidak ditemukan.');
			}
		} else {
			array_push($results['messages'], 'Berkas dan Nomor SPT sudah pernah dibuat.');
		}
		
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'bidang_id' => 'required',
      'anggaran_id' => 'required',
      'ppk_user_id' => 'required',
      'dasar_pelaksana' => 'required',
      'untuk' => 'required',
      'transportasi' => 'required',
      'ppk_user_id' => 'required',
      'provinsi_asal' => 'required',
      'kota_asal' => 'required',
      'provinsi_tujuan' => 'required',
      'kota_tujuan' => 'required',
      'tgl_berangkat' => 'required',
      'tgl_kembali' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			DB::transaction(function () use ($inputs) {
				$noMax = SPT::max('no_index') + 1 ?? 1;
				$tahun = Carbon::now()->format('Y');
				$noSpt = $noMax . '/01/SPPD/PDK/' . $tahun ;
				$spt = SPT::create([
					'no_index' => $noMax,
					'no_spt' => $noSpt,
					'bidang_id' => $inputs['bidang_id'],
					'anggaran_id' => $inputs['anggaran_id'],
					'ppk_user_id' => $inputs['ppk_user_id'],
					'dasar_pelaksana' => $inputs['dasar_pelaksana'],
					'untuk' => $inputs['untuk'],
					'transportasi' => $inputs['transportasi'],
					'ppk_user_id' => $inputs['ppk_user_id'],
					'provinsi_asal' => $inputs['provinsi_asal'],
					'kota_asal' => $inputs['kota_asal'],
					'kec_asal' => $inputs['kec_asal'],
					'provinsi_tujuan' => $inputs['provinsi_tujuan'],
					'kota_tujuan' => $inputs['kota_tujuan'],
					'kec_tujuan' => $inputs['kec_tujuan'],
					'tgl_berangkat' => $inputs['tgl_berangkat'],
					'tgl_kembali' => $inputs['tgl_kembali'],
					'status' => 'DRAFT',
					'periode' => '2021'
				]);

				foreach($inputs['user_id'] as $userid){
					$detail = SPTDetail::create([
						'spt_id' => $spt->id,
						'user_id' => $userid
					]);
				}
			});
	
			array_push($results['messages'], 'Berhasil menambahkan SPT baru.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e){
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$data = SPT::join('anggaran as ag', 'ag.id', 'anggaran_id')
		->where('spt.id', $id)
		->select(
			'spt.*',
			'ag.mak as anggaran_text'
		)->first();

		$data->user_id = SPTDetail::where('spt_id', $id)->get()->pluck('user_id');
		$results['data'] = $data;

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'bidang_id' => 'required',
      'anggaran_id' => 'required',
      'ppk_user_id' => 'required',
      'dasar_pelaksana' => 'required',
      'untuk' => 'required',
      'transportasi' => 'required',
      'ppk_user_id' => 'required',
      'provinsi_asal' => 'required',
      'kota_asal' => 'required',
      'provinsi_tujuan' => 'required',
      'kota_tujuan' => 'required',
      'tgl_berangkat' => 'required',
      'tgl_kembali' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			DB::transaction(function () use ($inputs, $id) {
				$spt = SPT::find($id);
				
				$updateSpt = $spt->update([
					'bidang_id' => $inputs['bidang_id'],
					'anggaran_id' => $inputs['anggaran_id'],
					'ppk_user_id' => $inputs['ppk_user_id'],
					'dasar_pelaksana' => $inputs['dasar_pelaksana'],
					'untuk' => $inputs['untuk'],
					'transportasi' => $inputs['transportasi'],
					'ppk_user_id' => $inputs['ppk_user_id'],
					'provinsi_asal' => $inputs['provinsi_asal'],
					'kota_asal' => $inputs['kota_asal'],
					'kec_asal' => $inputs['kec_asal'],
					'provinsi_tujuan' => $inputs['provinsi_tujuan'],
					'kota_tujuan' => $inputs['kota_tujuan'],
					'kec_tujuan' => $inputs['kec_tujuan'],
					'tgl_berangkat' => $inputs['tgl_berangkat'],
					'tgl_kembali' => $inputs['tgl_kembali']
				]);

				//Delete Missing User Id
				$data = SPTDetail::where('spt_id', $id)
				->whereNotIn('id', $inputs['user_id'])
				->delete();

				//Insert new or skip
				foreach($inputs['user_id'] as $userid){
					$detailSPT = SPTDetail::where('user_id', $userid)->where('spt_id', $id)->first();
					if ($detailSPT == null ){
						$detail = SPTDetail::create([
							'spt_id' => $id,
							'user_id' => $userid
						]);
					}
				}
			});
	
			array_push($results['messages'], 'Berhasil mengubah SPT.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e){
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	private function removeMissingDetails(&$respon, $id, $details)
  {
    $ids = Array();
    foreach($details as $dt){
      array_push($ids,$dt->id != null ? $dt->id :0);
    }

		$data = SPTDetail::where('spt_id', $id)
		->whereNotIn('id', $ids)
		->delete();

    return $respon;
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
	
				array_push($results['messages'], 'Berhasil menghapus data.');
				$results['state_code'] = 200;
				$results['success'] = true;
			} catch(\Exception $e){
				array_push($results['messages'], $e->getMessage());
			}
		} else {
			array_push($results['messages'], 'SPT tidak dapat dihapus!');
		}

		return response()->json($results, $results['state_code']);
	}
}
