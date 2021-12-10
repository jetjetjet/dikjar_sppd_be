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
		$cek = $user->hasRole('Super Admin') ? array_push($perm, 'is_admin') : false ;
		$token = $user->createToken($request->nip, $perm);

		$pathFoto = $user->path_foto != null ? $user->path_foto : '/storage/profile/user.png';
		$data = Array( "token" => $token->plainTextToken,
			"pegawai_id" => $user->pegawai->id,
			"email" => $user->pegawai->email,
			"full_name" => $user->pegawai->full_name,
			"nip" => $user->pegawai->nip,
			// "address" => $user->pegawai->address,
			// "phone" => $user->pegawai->phone,
			// "jenisKelamin" => $user->pegawai->jenis_kelamin,
			// "ttl" => $user->pegawai->ttl,
			"path_foto" => $pathFoto,
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
