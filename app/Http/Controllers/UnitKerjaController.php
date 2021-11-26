<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitKerja;
use DB;
use Validator;

class UnitKerjaController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = UnitKerja::orderBy('name')->get();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$results['data'] = UnitKerja::select('id', 'name')->get();
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
		
    UnitKerja::create([
      'name' => $inputs['name'],
      'remark' => $inputs['remark'] ?? null
    ]);

    array_push($results['messages'], 'Berhasil menambahkan UnitKerja baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = UnitKerja::find($id);

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

    
		$UnitKerja = UnitKerja::find($id);
    $UnitKerja->update([
      'name' => $inputs['name'],
      'remark' => $inputs['remark'] ?? null
    ]);

    array_push($results['messages'], 'Berhasil mengubah UnitKerja.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$role = UnitKerja::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus UnitKerja.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
