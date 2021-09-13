<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Validator;

use App\Helpers\Utils;

class UserController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function grid(Request $request)
	{
		$results = $this->responses;

		$results['data'] = User::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'nip' => 'required|unique:users,nip',
			'email' => 'required',
			'full_name' => 'required',
			'jenis_kelamin' => 'required',
			'jenis_kelamin' => 'required',
			'role' => 'required',
			'phone' => 'max:15'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
		try{
			$defaultPassword = bcrypt('12345678');
			$user = User::create([
				'nip' => $inputs['nip'],
				'full_name' => $inputs['full_name'],
				'password' => $defaultPassword,
				'email' => $inputs['email'],
				'jenis_kelamin' => $inputs['jenis_kelamin'],
				'address' => $inputs['address'] ?? null,
				'phone' => $inputs['phone'] ?? null,
				'ttl' => $inputs['ttl'] ?? null
			]);

			//asign role
			$user->assignRole($inputs['role']);

			//upload poto
			$file = Utils::imageUpload($request, 'profile');
			if($file != null) $user->path_foto = $file->path;

			array_push($results['messages'], 'Berhasil menambahkan user baru.');

			$results['success'] = true;
			$results['state_code'] = 200;
		}catch(\Exception $e){
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$results = $this->responses;
		$user = User::find($id);
		$user->role = $user->getRoleNames()[0] ?? null;

		$results['data'] = $user;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'nip' => 'required',
			'email' => 'required',
			'full_name' => 'required',
			'jenis_kelamin' => 'required',
			'role' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
		$user = User::find($id);
		$user->syncRoles([$inputs['role']]);
		$user->update([
			'nip' => $inputs['nip'],
			'full_name' => $inputs['full_name'],
			'email' => $inputs['email'],
			'jenis_kelamin' => $inputs['jenis_kelamin'],
			'address' => $inputs['address'] ?? null,
			'phone' => $inputs['phone'] ?? null,
			'tgl_lahir' => $inputs['tgl_lahir'] ?? null
		]);
		

		$results['success'] = true;
		$results['state_code'] = 200;
		array_push($results['messages'], 'Berhasil ubah user.');

		return response()->json($results, $results['state_code']);
	}

	public function changePassword(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'password' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }

		
		$user = User::find($id)
			->update([
				'password' => bcrypt($inputs['password'])
			]);

		$results['success'] = true;
		$results['state_code'] = 200;

		array_push($results['messages'], 'Berhasil ubah password.');

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
			$user = User::find($id)
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

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$results = $this->responses;
		if($id == 1){
			array_push($results['messages'], 'User ini tidak dapat dihapus.');
			return response()->json($results, $results['state_code']);
		}

		$role = User::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus user.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
