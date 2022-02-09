<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisTransport;
use DB;
use Validator;

class JenisTransportController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = JenisTransport::select(
			'id',
			'name',
			DB::raw("to_char(created_at, 'DD-MM-YYYY HH:MI') as tgl_buat"),
			DB::raw("to_char(updated_at, 'DD-MM-YYYY HH:MI') as tgl_ubah"),
		)->get();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$results['data'] = JenisTransport::all()->pluck('name');
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
    $kat = JenisTransport::whereRaw("upper(name) = '" . $upper . "'")->first();
		
		if($kat == null) {
			JenisTransport::create([
				'name' => $inputs['name']
			]);
			$results['success'] = true;
			$results['state_code'] = 200;
			array_push($results['messages'], 'Berhasil menambahkan Jenis Transportasi baru.');
		} else {
			$results['state_code'] = 400;
			array_push($results['messages'], 'Jenis Transportasi Sudah Ada!');
		}

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = JenisTransport::find($id);

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
    
		$JenisTransport = JenisTransport::find($id);
    $JenisTransport->update([
      'name' => $inputs['name']
    ]);

    array_push($results['messages'], 'Berhasil mengubah Jenis Transportasi.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$role = JenisTransport::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus Jenis Transportasi.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
