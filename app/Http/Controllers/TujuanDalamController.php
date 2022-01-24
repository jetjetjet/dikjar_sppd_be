<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilayah\Kecamatan;
use DB;
use Validator;

class TujuanDalamController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = Kecamatan::select(
			'id',
			'name'
		)->orderBy('name')
    ->get();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$results['data'] = Kecamatan::all()->pluck('name');
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
    $kat = Kecamatan::whereRaw("upper(name) = '" . $upper . "'")->first();
		
		if($kat == null) {
			Kecamatan::create([
				'kabupaten_id' => 1501,
				'name' => $inputs['name']
			]);
		}

    array_push($results['messages'], 'Berhasil menambahkan Tujuan Dalam Daerah baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Kecamatan::find($id);

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
    
		$Kecamatan = Kecamatan::find($id);
    $Kecamatan->update([
      'name' => $inputs['name']
    ]);

    array_push($results['messages'], 'Berhasil mengubah Tujuan Dalam Daerah.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$role = Kecamatan::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus Tujuan Dalam Daerah.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
