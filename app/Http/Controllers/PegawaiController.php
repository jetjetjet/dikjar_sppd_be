<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use DB;
use Validator;

use App\Helpers\Utils;

class PegawaiController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;

		$results['data'] = Pegawai::join('jabatan as j', 'j.id', 'jabatan_id')
		->select('pegawai.id', 'nip', 'full_name', 'j.name as jabatan')
		->orderBy('pegawai.created_at')->get();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$q = Pegawai::select('pegawai.id as code', 'full_name as label');
		if($request->filter){
			if($request->filter == 'parent'){
				$q = $q->join('jabatan as j', 'j.id', 'pegawai.jabatan_id')
				->where('j.is_parent', '1');
			}

			if($request->filter == 'spt'){
				$q = $q->whereRaw("id not in ( select pegawai_id from spt_detail where deleted_at is null and finished_at is null )")
					->where('pegawai_app', '1');
			}

			if($request->filter == 'spt_edit' && isset($request->id)){
				$q = $q->whereRaw("id not in ( select pegawai_id from spt_detail where deleted_at is null and finished_at is null and spt_id not in ( ". $request->id ." ) )")
					->where('pegawai_app', '1');
			}
		}

		$results['data'] = $q->get();
		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'nip' => 'required',
			'email' => 'required',
			'full_name' => 'required',
			'jenis_kelamin' => 'required',
			'phone' => 'max:15'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
		try{
			$pegawai = Pegawai::create([
				'nip' => $inputs['nip'],
				'full_name' => $inputs['full_name'],
				'jabatan_id' => $inputs['jabatan_id'],
				'email' => $inputs['email'],
				'jenis_kelamin' => $inputs['jenis_kelamin'],
				'address' => $inputs['address'] ?? null,
				'phone' => $inputs['phone'] ?? null,
				'ttl' => $inputs['ttl'] ?? null
			]);

			//upload poto
			$file = Utils::imageUpload($request, 'profile');
			if($file != null) $pegawai->path_foto = $file->path;

			array_push($results['messages'], 'Berhasil menambahkan pegawai baru.');

			$results['success'] = true;
			$results['state_code'] = 200;
		}catch(\Exception $e){
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$pegawai = Pegawai::find($id);

		$results['data'] = $pegawai;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'nip' => 'required',
			'email' => 'required',
			'full_name' => 'required',
			'jenis_kelamin' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
		$pegawai = Pegawai::find($id);
		$pegawai->update([
			'nip' => $inputs['nip'],
			'full_name' => $inputs['full_name'],
			'email' => $inputs['email'],
			'jenis_kelamin' => $inputs['jenis_kelamin'],
			'jabatan_id' => $inputs['jabatan_id'],
			'address' => $inputs['address'] ?? null,
			'phone' => $inputs['phone'] ?? null,
			'tgl_lahir' => $inputs['tgl_lahir'] ?? null
		]);

		$results['success'] = true;
		$results['state_code'] = 200;
		array_push($results['messages'], 'Berhasil ubah pegawai.');

		return response()->json($results, $results['state_code']);
	}

	public function changePhoto(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'file' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }

		try{
			DB::beginTransaction();
		
			//upload poto
			$file = Utils::imageUpload($inputs, 'profile');
			$pegawai = Pegawai::find($id)
			->update([
				'path_foto' => $file->path
			]);
			DB::commit();

			array_push($results['messages'], 'Berhasil ubah poto.');

			$results['data'] = array('path_foto' => $file->path);
			$results['success'] = true;
			$results['state_code'] = 200;
		}catch(\Exception $e){
			array_push($results['messages'], $e->getMessage());
			DB::rollBack();
		}

		return response()->json($results, $results['state_code']);
	}

	public function destroy($id)
	{
		$results = $this->responses;
		if($id == 1){
			array_push($results['messages'], 'Pegawai ini tidak dapat dihapus.');
			return response()->json($results, $results['state_code']);
		}

		$pegawai = Pegawai::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus pegawai.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}