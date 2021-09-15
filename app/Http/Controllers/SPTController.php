<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use DB;
use Validator;


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

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'no_spt' => '1/Tes/2021',
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
				$spt = SPT::create([
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
			array_push($results['messages'], $e);
		}

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = PejabatTtd::join('users', 'users.id', 'user_id')
		->where('pejabat_ttd.id', $id)
		->select(
			'pejabat_ttd.id as id',
			'user_id',
			'nip',
			'autorisasi',
			DB::raw("case when is_active is true then 'Aktif' else 'Tidak Aktif' end as status_aktif"),
			'is_active'
		)->first();

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'user_id' => 'required',
      'autorisasi' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
    
		$PejabatTtd = PejabatTtd::find($id);

		$code = $inputs['autorisasi'] == 'Pejabat Pembuat Komitmen' ? 'PPK' : 'PTTD';
    $PejabatTtd->update([
      'user_id' => $inputs['user_id'],
      'autorisasi' => $inputs['autorisasi'],
      'autorisasi_code' => $code
    ]);

    array_push($results['messages'], 'Berhasil mengubah data.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;

		$role = PejabatTtd::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus data.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
