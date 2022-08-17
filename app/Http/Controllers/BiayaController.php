<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;

class BiayaController extends Controller
{
	public function grid(Request $request, $id, $pegawaiId)
	{
		$results = $this->responses;

		$biaya = DB::select("select * from biaya_grid({$id}, {$pegawaiId})");

		$total = 0;
		foreach ($biaya as $b) {
			$total += $b->biaya;
		}

		$results['data'][] = [
			'tipe' => 'Total Pengeluaran',
			'biaya' => $total,
			'children' => $biaya
		];
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'spt_id' 	=> 'required',
      'pegawai_id' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
    $biaya = Biaya::create([
      'pegawai_id' => $inputs['pegawai_id'],
      'spt_id' => $inputs['spt_id'],
			'uang_makan' => $inputs['uang_makan'],
			'uang_saku' => $inputs['uang_saku'],
			'uang_representasi' => $inputs['uang_representasi'],
			// 'uang_inap' => $inputs['uang_inap'],
			// 'uang_travel' => $inputs['uang_travel'],
			// 'uang_pesawat' => $inputs['uang_pesawat'],
			'jml_biaya' => $inputs['jml_biaya']
    ]);

		$results['data'] = array( 'biaya_id' => $biaya->id);
    array_push($results['messages'], 'Berhasil menambahkan Biaya.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function update (Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();

		$biaya = Biaya::find($id);
		$biaya->update([
			'uang_makan' => $inputs['uang_makan'],
			'uang_saku' => $inputs['uang_saku'],
			'uang_representasi' => $inputs['uang_representasi'],
			'jml_biaya' => $inputs['jml_biaya']
		]);
    array_push($results['messages'], 'Berhasil memperbarui Biaya.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
}
