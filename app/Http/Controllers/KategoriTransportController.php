<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriTransport;
use DB;
use Validator;

class KategoriTransportController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = KategoriTransport::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$results['data'] = KategoriTransport::all()->pluck('name');
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
		
    KategoriTransport::create([
      'name' => $inputs['name']
    ]);

    array_push($results['messages'], 'Berhasil menambahkan Kategori Transportasi baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = KategoriTransport::find($id);

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
    
		$KategoriTransport = KategoriTransport::find($id);
    $KategoriTransport->update([
      'name' => $inputs['name']
    ]);

    array_push($results['messages'], 'Berhasil mengubah Kategori Transportasi.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$role = KategoriTransport::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus Kategori Transportasi.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
