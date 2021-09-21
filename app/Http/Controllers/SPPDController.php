<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\User; 
use App\Models\Jabatan;
use DB;
use Validator;
use Carbon\Carbon;

class SPPDController extends Controller
{
  
	public function grid($id)
	{
		$results = $this->responses;
		$results['data']  = SPT::join('spt_detail as sd', 'sd.spt_id', 'spt.id')
		->join('users as u', 'u.id', 'sd.user_id')
		->whereNull('sd.deleted_at')
    ->select(
      'u.id as user_id',
      'full_name',
      'nip'
    )->get();

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

  public function show($id, $userId)
  {
		$results = $this->responses;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
  }
}
