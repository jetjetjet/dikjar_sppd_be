<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use DB;
use Validator;

class BidangController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = Bidang::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$results['data'] = Bidang::select('id', 'name')->get();
		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'code' => 'required',
      'name' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
    Bidang::create([
      'code' => $inputs['code'],
      'name' => $inputs['name'],
      'remark' => $inputs['remark'] ?? null
    ]);

    array_push($results['messages'], 'Berhasil menambahkan Bidang baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Bidang::find($id);

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'code' => 'required',
      'name' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }

    
		$Bidang = Bidang::find($id);
    $Bidang->update([
      'code' => $inputs['code'],
      'name' => $inputs['name'],
      'remark' => $inputs['remark'] ?? null
    ]);

    array_push($results['messages'], 'Berhasil mengubah Bidang.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$role = Bidang::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus Bidang.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
