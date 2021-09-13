<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use DB;
use Validator;

use App\Helpers\Utils;

class AnggaranController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = Anggaran::where('periode', '2021')->get();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		dd($inputs);
		$rules = array(
			'mak' => 'required',
      'uraian' => 'required',
      'pagu' => 'required|numeric|min:4'
		);

		$validator = Validator::make($inputs, $rules);
		dd($validator->fails());
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
    Anggaran::create([
      'mak' => $inputs['mak'],
      'uraian' => $inputs['uraian'],
      'pagu' => $inputs['pagu'],
      'periode' => '2021'
    ]);

    array_push($results['messages'], 'Berhasil menambahkan Anggaran baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Anggaran::find($id);

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'uraian' => 'required',
      'pagu' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
    
		$anggaran = Anggaran::find($id);
    $anggaran->update([
      'uraian' => $inputs['uraian'],
      'pagu' => $inputs['pagu'],
    ]);

    array_push($results['messages'], 'Berhasil mengubah Anggaran.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;

    //Validasi

		$role = Anggaran::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus anggaran.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
