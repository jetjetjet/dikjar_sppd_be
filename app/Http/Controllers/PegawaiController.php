<?php

namespace App\Http\Controllers;

use App\Http\Requests\PegawaiRequest;
Use App\Services\PegawaiService;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\User;
use DB;
use Validator;

use App\Helpers\Utils;

class PegawaiController extends Controller
{
	/**
	 * 
	 */
	public function __construct(protected PegawaiService $service)
	{
		//
	}

	public function grid(Request $request)
	{
		$results = $this->service->getAll();
		// $data = Pegawai::select('pegawai.id', 'nip', 'full_name', 'golongan', 'pangkat', 'jabatan', 'status_pegawai')
		// ->orderBy('pegawai.id')->get();

		return $this->response($results, true, [], 200);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$q = Pegawai::select('pegawai.id as code', 'full_name as label');
		if($request->filter){
			if($request->filter == 'all'){
				//
			} else if($request->filter == 'spt'){
				$q = $q->whereRaw("id not in ( select pegawai_id from spt_detail sd join spt on spt.id = sd.spt_id where status in ('KONSEP', 'PROSES', 'KWITANSI', 'KEMBALI') and spt.deleted_at is null and sd.deleted_at is null )")
					->where('pegawai_app', '1');
			} else if($request->filter == 'spt_edit' && isset($request->id)){
				$q = $q->whereRaw("id not in ( select pegawai_id from spt_detail sd join spt on spt.id = sd.spt_id where status in ('KONSEP', 'PROSES', 'KWITANSI', 'KEMBALI') and spt.deleted_at is null and sd.deleted_at is null and spt_id != ". $request->id ." )")
					->where('pegawai_app', '1');
			} else if($request->filter == 'user'){
				$q = Pegawai::select('pegawai.email as code', 'full_name as label')
				->whereRaw("email not in ( select email from users )")->where('pegawai_app', '1');
			} else if($request->filter == 'report' ){
				$q = $q->where('pegawai_app', '1');
			} 
		}

		$results['data'] = $q->aktif()->get();
		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$inputs['file'] = $inputs['file'] != 'null' ? $inputs['file'] : null;
		$rules = array(
			// 'nip' => 'required|unique:pegawai,nip',
			'email' => 'required',
			'full_name' => 'required',
			'jabatan' => 'required',
			// 'pangkat' => 'required',
			// 'golongan' => 'required',
			'jenis_kelamin' => 'required',
			'phone' => 'max:15',
			'status_pegawai' => 'required',
			// 'file' => 'mimes:jpeg,bmp,png,gif'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
			$results['messages'] = Array($validator->messages()->first());
			return response()->json($results, $results['state_code']);
		}
		
		try{
			DB::beginTransaction();

			$pegawai = Pegawai::create([
				'nip' => $inputs['nip'] == 'null' ? null : $inputs['nip'],
				'full_name' => $inputs['full_name'],
				'jabatan' => $inputs['jabatan'],
				'pangkat' => $inputs['pangkat'] == 'null' ? null : $inputs['pangkat'],
				'golongan' => $inputs['golongan'] == 'null' ? null : $inputs['golongan'],
				'email' => $inputs['email'],
				'jenis_kelamin' => $inputs['jenis_kelamin'],
				'status_pegawai' => $inputs['status_pegawai'],
				'address' => $inputs['address'] == 'null' ? null : $inputs['address'],
				'phone' => $inputs['phone'] == 'null' ? null : $inputs['phone'],
				'tgl_lahir' => json_decode($inputs['tgl_lahir']) ?? null,
				'pegawai_app' => '1'
			]);

			if($inputs['pegawai_app']) {
				User::create([
					'email' => $inputs['email'],
					'password' => bcrypt('password')
				]);
			}

			//upload poto
			$file = Utils::imageUpload($request, 'profile');
			if($file != null) $pegawai->path_foto = $file->path;

			DB::commit();

			array_push($results['messages'], 'Berhasil menambahkan pegawai baru.');
			$results['success'] = true;
			$results['state_code'] = 200;
		}catch(\Exception $e){
			DB::rollBack();
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$pegawai = Pegawai::find($id);
		$pegawai->path_foto = $pegawai->path_foto != null ? $pegawai->path_foto : '/storage/profile/user.png';

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
			// 'nip' => 'required',
			// 'golongan' => 'required',
			// 'pangkat' => 'required',
			'status_pegawai' => 'required',
			'jabatan' => 'required',
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
			'pangkat' => $inputs['pangkat'],
			'golongan' => $inputs['golongan'],
			'email' => $inputs['email'],
			'jenis_kelamin' => $inputs['jenis_kelamin'],
			'jabatan' => $inputs['jabatan'],
			'address' => $inputs['address'] ?? null,
			'phone' => $inputs['phone'] ?? null,
			'tgl_lahir' => $inputs['tgl_lahir'] ?? null,
			'status_pegawai' => $inputs['status_pegawai'],
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
