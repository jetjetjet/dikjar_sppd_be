<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Desa;
use DB;
use Validator;

use App\Helpers\Utils;

class WilayahController extends Controller
{
	public function getProvinsi(Request $request)
	{
		$results = $this->responses;
		$cari = $request->q ?? '';
		$results['data'] = Provinsi::whereRaw('UPPER(name) LIKE UPPER(\'%'. $cari .'%\')')
		->select('id', 'name')
		->orderBy('name')
		->limit(10)
		->get();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

  public function getKabupaten(Request $request)
	{
		$results = $this->responses;
		$inputs = $request->all();

		$cari = $request['q'] ?? '';

		$results['data'] = Kabupaten::whereRaw('UPPER(name) LIKE UPPER(\'%'. $cari .'%\')')
		->select('id', 'name')
		->orderBy('name')
		->limit(10)
		->get();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
	
  public function getKecamatan(Request $request)
	{
		$results = $this->responses;
		$inputs = $request->all();

		$cari = $request['q'] ?? '';
		$kota_id = 1501; // ID Kabupaten Kerinci

		$results['data'] = Kecamatan::where('kabupaten_id', $kota_id)
		->whereRaw('UPPER(name) LIKE UPPER(\'%'. $cari .'%\')')
		->select('id', 'name')
		->orderBy('name')
		->limit(10)
		->get();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
	
  public function getDesa(Request $request)
	{
		$results = $this->responses;
		$inputs = $request->all();
		$rules = array(
			'kecamatan_id' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }

		$cari = $request['q'] ?? '';
		$kecamatan_id = $inputs['kecamatan_id'];

		$results['data'] = Desa::where('kecamatan_id', $kecamatan_id)
		->whereRaw('UPPER(name) LIKE UPPER(\'%'. $cari .'%\')')
		->select('id', 'name')
		->orderBy('name')
		->limit(10)
		->get();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
