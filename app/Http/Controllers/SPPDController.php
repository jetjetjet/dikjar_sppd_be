<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\Transport;
use App\Models\Inap;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SPPDController extends Controller
{
  
	public function grid($id)
	{
		$results = $this->responses;

		$header = SPT::where('id', $id)
		->select(
			'no_spt',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tgl_kembali"),
			DB::raw("coalesce(kota_tujuan, kec_tujuan) as kota_tujuan"),
			DB::raw("coalesce(kota_asal, kec_asal) as kota_asal"),
			'transportasi'
		)->first();
		
		$child = [];
		if ($header != null){
			$child = SPTDetail::join('users as u', 'u.id', 'spt_detail.user_id')
			->where('spt_id', $id)
			->select(
				'spt_detail.id',
				'u.id as user_id',
				'full_name',
				'nip'
			)->get();
		}

		$results['data']  = array( 'header' => $header, 'child' => $child);

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

  public function show($id, $sptDetailId, $userId)
  {
		$results = $this->responses;

		$header = SPT::where('id', $id)
		->select(
			'no_spt',
			'tgl_berangkat',
			'tgl_kembali',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tglb_text"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tglk_text"),
			DB::raw("coalesce(kota_tujuan, kec_tujuan) as kota_tujuan"),
			DB::raw("coalesce(kota_asal, kec_asal) as kota_asal"),
			'transportasi'
		)->first();

		$child = null;
		if ($header != null){
			$check = Biaya::where('spt_id', $id)
			->where('user_id', $userId)
			->select(
				'id',
				'uang_makan',
				'uang_saku',
				'uang_representasi',
				'uang_inap',
				'uang_travel',
				'uang_pesawat',
				'jml_biaya',
			)->first();

			if($check != null){
				$child = $check;
			}
		}
		
		$results['data']  = array( 'header' => $header, 'child' => $child);
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
  }
}
