<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Validator;

use App\Helpers\Utils;

class ProfileController extends Controller
{
	public function show($id)
	{
		$results = $this->responses;

		if($id != auth('sanctum')->user()->id){
			array_push($results['messages'], 'Tidak dapet mengubah user!');
      return response()->json($results, $results['state_code']);
		}

		$user = User::find($id);
		$user->role = $user->getRoleNames()[0] ?? null;

		$results['data'] = $user;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		if($id != auth('sanctum')->user()->id){
			array_push($results['messages'], 'Tidak dapet mengubah user!');
      return response()->json($results, $results['state_code']);
		}

		$inputs = $request->all();
		$rules = array(
			'email' => 'required',
			'full_name' => 'required',
			'jenis_kelamin' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
		$user = User::find($id)
			->update([
				'full_name' => $inputs['full_name'],
				'email' => $inputs['email'],
				'jenis_kelamin' => $inputs['jenis_kelamin'],
				'address' => $inputs['address'] ?? null,
				'phone' => $inputs['phone'] ?? null,
				'ttl' => $inputs['ttl'] ?? null
			]);

		$results['success'] = true;
		$results['state_code'] = 200;
		array_push($results['messages'], 'Berhasil ubah user.');

		return response()->json($results, $results['state_code']);
	}

	public function changePassword(Request $request, $id)
	{
		$results = $this->responses;

		if($id != auth('sanctum')->user()->id){
			array_push($results['messages'], 'Tidak dapet mengubah user!');
      return response()->json($results, $results['state_code']);
		}

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

		if($id != auth('sanctum')->user()->id){
			array_push($results['messages'], 'Tidak dapet mengubah user!');
      return response()->json($results, $results['state_code']);
		}

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
}
