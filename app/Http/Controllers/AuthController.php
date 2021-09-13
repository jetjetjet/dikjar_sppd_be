<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;

class AuthController extends Controller
{
	public function login(Request $request)
	{
    $results = $this->responses;

		$rules = array(
			'nip' => 'required',
			'password' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
    // $user = User::where('nip', $request->nip)->firstOrFail();
		$ingat = $request->remember ? true : false;
		$masuk = $request->only('nip', 'password');
		if (!Auth::attempt($masuk, $ingat)){
			array_push($results['messages'], 'Username atau Passworsd Salah');
			return response()->json($results, $results['state_code']);
		};
		
    $user = Auth::user();
		$perm = $user->getAllPermissions()->pluck('name')->toArray();
		$token = $user->createToken($request->nip, $perm);

		$data = Array( "token" => $token->plainTextToken,
			"userid" => $user->id,
			"username" => $user->username,
			"email" => $user->email,
			"full_name" => $user->full_name,
			"nip" => $user->nip,
			"address" => $user->address,
			"phone" => $user->phone,
			"jenis_kelamin" => $user->jenis_kelamin,
			"ttl" => $user->ttl,
			"perms" => $perm
		);
		$results['success'] = true;
		$results['state_code'] = 200;
		$results['data'] = $data;

		return response()->json($results, $results['state_code']);
	}

	public function logout()
  {
    $user = auth('sanctum')->user();
    $idUser = $user->id ?? 0;
    $user->currentAccessToken()->delete();
		
    return response()->json(['success' => true], 200);
  }
}
