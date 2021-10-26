<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use DB;
use Validator;

class JabatanController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = Jabatan::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$q = Jabatan::select('id', 'name');
		if($request->filter){
			if($request->filter == 'edit' && isset($request->id)){
				$q = $q->whereRaw("jabatan.id not in ( select jabatan_id from pegawai where deleted_at is null and (jabatan_id is not null and jabatan_id not in (".$request->id.") ) )");
			}
		} else {
			$q = $q->whereNotIn('jabatan.id', [DB::raw("select jabatan_id from pegawai where deleted_at is null and jabatan_id is not null")]);
		}

		$results['data'] = $q->get();
		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function parent(Request $request)
	{
		$results = $this->responses;
		
		$results['data'] = Jabatan::where("is_parent", "1")
		->select('id', 'name')->get();

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Jabatan::find($id);

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'name' => 'required',
      'golongan' => 'required',
      'is_parent' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
    Jabatan::create([
      // 'bidang_id' => $inputs['bidang_id'],
      'name' => $inputs['name'],
      'golongan' => $inputs['golongan'],
      'remark' => $inputs['remark'],
      'is_parent' => $inputs['is_parent'],
      'parent_id' => $inputs['parent_id']
    ]);

    array_push($results['messages'], 'Berhasil menambahkan Jabatan baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'name' => 'required',
      'golongan' => 'required',
      'is_parent' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
    
		$Jabatan = Jabatan::find($id);
    $Jabatan->update([
      // 'bidang_id' => $inputs['bidang_id'],
      'name' => $inputs['name'],
      'golongan' => $inputs['golongan'],
      'remark' => $inputs['remark'],
      'is_parent' => $inputs['is_parent'],
      'parent_id' => $inputs['parent_id']
    ]);

    array_push($results['messages'], 'Berhasil mengubah Jabatan.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function destroy($id)
	{
		$results = $this->responses;
		$Jabatan = Jabatan::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus Bidang.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
