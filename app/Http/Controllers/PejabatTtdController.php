<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PejabatTtd;
use DB;
use Validator;

class PejabatTtdController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = PejabatTtd::join('users', 'users.id', 'user_id')
		->select(
			'pejabat_ttd.id as id',
			'nip',
			'full_name',
			'autorisasi',
			DB::raw("case when is_active is true then 'Aktif' else 'Tidak Aktif' end as status_aktif"),
			'is_active'
		)->get();

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		if($request->has('filter')){
			$results['data'] = PejabatTtd::join('users', 'users.id', 'user_id')
			->where('is_active', '1')
			->where('autorisasi_code', $request->filter)
			->select('pejabat_ttd.id as id', 'full_name as label')->get();

			$results['state_code'] = 200;
			$results['success'] = true;
		}

		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'user_id' => 'required',
      'autorisasi' => 'required',
      'is_active' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		$code = $inputs['autorisasi'] == 'Pejabat Pembuat Komitmen' ? 'PPK' : 'PTTD';
    PejabatTtd::create([
      'user_id' => $inputs['user_id'],
      'autorisasi' => $inputs['autorisasi'],
      'autorisasi_code' => $code,
      'is_active' => $inputs['is_active']
    ]);

    array_push($results['messages'], 'Berhasil menambahkan data baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

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

	public function setActive(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'is_active' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }

		$pejabat = PejabatTtd::find($id);
		$pejabat->is_active = $inputs['is_active'];
		$pejabat->save();

		array_push($results['messages'], 'Berhasil mengubah status aktif.');
		$results['state_code'] = 200;
		$results['success'] = true;

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
