<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satuan;
use DB;
use Validator;

class SatuanController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = Satuan::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$results['data'] = Satuan::all()->pluck('name');
		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'name' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		$upper = strtoupper($inputs['name']);
    $kat = Satuan::whereRaw("upper(name) = '" . $upper . "'")->first();
		
		if($kat == null) {
			Satuan::create([
				'name' => $inputs['name']
			]);
		}

    array_push($results['messages'], 'Berhasil menambahkan Satuan baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Satuan::find($id);

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'name' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
    
		$Satuan = Satuan::find($id);
    $Satuan->update([
      'name' => $inputs['name']
    ]);

    array_push($results['messages'], 'Berhasil mengubah Satuan.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$role = Satuan::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus Satuan.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
