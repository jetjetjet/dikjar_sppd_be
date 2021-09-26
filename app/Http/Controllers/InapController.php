<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inap;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InapController extends Controller
{
	public function grid($biayaId, $userId)
	{
		$results = $this->responses;
		$results['data'] = Inap::where('biaya_id', $biayaId)
		->where('user_id', $userId)
		->select(
			'id',
			'hotel',
			'room',
			'harga',
			'tgl_checkin',
			'tgl_checkout',
			DB::raw("to_char(tgl_checkin, 'DD-MM-YYYY') as checkin_text"),
			DB::raw("to_char(tgl_checkout, 'DD-MM-YYYY') as checkout_text"),
			'jml_bayar',
			'jml_hari',
			'checkout_at'
		)
		->get();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

  public function store(Request $request)
	{
		$results = $this->responses;
		
		$inputs = $request->all();
		$rules = array(
			'user_id' 	=> 'required',
      'biaya_id' => 'required',
      'hotel' => 'required',
      'room' => 'required',
      'harga' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
    Inap::create([
      'user_id' => $inputs['user_id'],
      'biaya_id' => $inputs['biaya_id'],
			'hotel' => $inputs['hotel'],
			'room' => $inputs['room'],
			'harga' => $inputs['harga'],
			'tgl_checkin' => $inputs['tgl_checkin']
    ]);

		// $results['data'] = array( 'biaya_id' => $biaya->id);
    array_push($results['messages'], 'Berhasil menambah data Hotel.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function update (Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'user_id' 	=> 'required',
      'biaya_id' => 'required',
      'hotel' => 'required',
      'room' => 'required',
      'harga' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		$inap = Inap::where('id',$id)
		->where('user_id', $inputs['user_id'])
		->where('biaya_id', $inputs['biaya_id'])
		->first();

		$inap->update([
			'hotel' => $inputs['hotel'],
			'room' => $inputs['room'],
			'harga' => $inputs['harga'],
			'tgl_checkin' => $inputs['tgl_checkin']
		]);
    array_push($results['messages'], 'Berhasil mengubah data checkin.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function checkout(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'user_id' 	=> 'required',
      'biaya_id' => 'required',
      'tgl_checkout' => 'required',
      'jml_hari' => 'required',
      'jml_bayar' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try{
			DB::transaction(function () use ($inputs, $id) {
				$inap = Inap::where('id',$id)
				->where('user_id', $inputs['user_id'])
				->where('biaya_id', $inputs['biaya_id'])
				->first();

				$inap->update([
					'tgl_checkout' => $inputs['tgl_checkout'],
					'jml_hari' => $inputs['jml_hari'],
					'jml_bayar' => $inputs['jml_bayar'],
					'catatan' => $inputs['catatan'],
					'checkout_at' => DB::raw("now()")
				]);
		
				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('user_id', $inputs['user_id'])
				->first();
				$biaya->update([
					'uang_inap' => ($biaya->uang_inap ?? 0) + $inputs['jml_bayar'],
					'jml_biaya' => $biaya->jml_biaya + $inputs['jml_bayar']
				]);
		
			});
		
			array_push($results['messages'], 'Berhasil menyimpan data checkout.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('transport_save_err: '. json_encode($e->getMessage()));
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function uploadFile(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'user_id' 	=> 'required',
      'biaya_id' => 'required',
      'file' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		$inap = Inap::where('id',$id)
		->where('user_id', $inputs['user_id'])
		->where('biaya_id', $inputs['biaya_id'])
		->first();

		$inap->update([
			'file_id' => $inputs['tgl_checkout']
		]);
    array_push($results['messages'], 'Berhasil menyimpan data checkout.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function destroy($id, $biayaId, $userId)
	{
		$results = $this->responses;

		$inap = Inap::where('id',$id)
		->where('user_id', $userId)
		->where('biaya_id', $biayaId)
		->first();
		
		try{
			DB::transaction(function () use ($inap, $biayaId, $userId) {
				$jml_bayar = $inap->jml_bayar;

				if(!empty($inap->checkout_at)){
					$biaya = Biaya::where('id', $biayaId)
					->where('user_id', $userId)
					->first();
					
					$biaya->update([
						'uang_inap' => $biaya->uang_inap -  $jml_bayar,
						'jml_biaya' => $biaya->jml_biaya - $jml_bayar
					]);
				}

				//delete
				$inap->delete();
			});
			
			array_push($results['messages'], 'Berhasil menghapus data Penginapan.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('transport_delete: '. json_encode($e->getMessage()));
			array_push($results['messages'], $e->getMessage());
		}
		return response()->json($results, $results['state_code']);
	}
}
