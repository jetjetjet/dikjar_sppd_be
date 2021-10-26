<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Validator;

use App\Helpers\Utils;

class UserController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;

		$results['data'] = User::join('pegawai as p', 'p.nip', 'users.nip')
		->join('jabatan as j', 'j.id', 'p.jabatan_id')
		->select(
			'users.id',
			'p.nip',
			'full_name',
			'j.name as jabatan'
		)->get();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'nip' => 'required|unique:users,nip',
			'role' => 'required',
			'password' => 'required'
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
				'password' => bcrypt($inputs['password'])
			]);

			//asign role
			$user->assignRole($inputs['role']);

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
		$user->full_name = $user->pegawai->full_name; 

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
			'role' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
		$user = User::find($id);
		$role = $inputs['role'] ?? null;
		$user->syncRoles([$role]);
		if($inputs['password'] != null) {
			$user->update([
				'password' => bcrypt($inputs['password'])
			]);
		}
		
		$results['success'] = true;
		$results['state_code'] = 200;
		array_push($results['messages'], 'Berhasil ubah User.');

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
