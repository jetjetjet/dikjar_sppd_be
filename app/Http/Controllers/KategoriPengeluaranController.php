<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPengeluaran;
use DB;
use Validator;

class KategoriPengeluaranController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = KategoriPengeluaran::select(
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
		
		$results['data'] = KategoriPengeluaran::all()->pluck('name');
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
    $kat = KategoriPengeluaran::whereRaw("upper(name) = '" . $upper . "'")->first();
		
		if($kat == null) {
			KategoriPengeluaran::create([
				'name' => $inputs['name']
			]);
		}

    array_push($results['messages'], 'Berhasil menambahkan Kategori Pengeluaran Baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = KategoriPengeluaran::find($id);

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
    
		$KategoriPengeluaran = KategoriPengeluaran::find($id);
    $KategoriPengeluaran->update([
      'name' => $inputs['name']
    ]);

    array_push($results['messages'], 'Berhasil mengubah Kategori Pengeluaran.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$role = KategoriPengeluaran::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus Kategori Pengeluaran.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
