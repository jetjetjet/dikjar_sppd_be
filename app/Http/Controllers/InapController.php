<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inap;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Helpers\Utils;

class InapController extends Controller
{
	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Inap::find($id);
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function update (Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'pegawai_id' 	=> 'required',
      'biaya_id' => 'required',
      'hotel' => 'required',
      'room' => 'required',
      'harga' => 'nullable',
      'tgl_checkin' => 'required',
      'tgl_checkout' => 'required',
      'jml_hari' => 'required',
      'total_bayar' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			DB::transaction(function () use ($inputs, $id, &$results) {
				$inap = Inap::where('id',$id)
				->where('pegawai_id', $inputs['pegawai_id'])
				->where('biaya_id', $inputs['biaya_id'])
				->first();

				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('pegawai_id', $inputs['pegawai_id'])
				->first();
				
				$total_bayar = $inap->total_bayar - $inputs['total_bayar'];
		
				$inap->update([
					'hotel' => $inputs['hotel'],
					'room' => $inputs['room'],
					'harga' => $inputs['harga'] == null ? 0 : $inputs['harga'],
					'tgl_checkin' => $inputs['tgl_checkin'],
					'tgl_checkout' => $inputs['tgl_checkout'],
					'total_bayar' => $inputs['total_bayar'],
					'jml_hari' => $inputs['jml_hari'],
					'catatan' => $inputs['catatan']
				]);

				$totalBiaya = $biaya->total_biaya - ($total_bayar);
				$biaya->update([
					'total_biaya_inap' => $biaya->total_biaya_inap - ($total_bayar),
					'total_biaya' => $totalBiaya
				]);
				$results['data'] = ['total' => $totalBiaya];
			});
			
			$results['success'] = true;
			$results['state_code'] = 200;
			array_push($results['messages'], 'Berhasil mengubah data checkin.');
		}  catch(\Exception $e) {
			Log::channel('spderr')->info('inap_update: '. json_encode($e->getMessage()));
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'pegawai_id' 	=> 'required',
      'biaya_id' => 'required',
      'hotel' => 'required',
      'room' => 'required',
      'harga' => 'nullable',
      'tgl_checkin' => 'required',
      'tgl_checkout' => 'required',
      'jml_hari' => 'required',
      'total_bayar' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try{
			DB::transaction(function () use ($inputs, &$results) {
				$inap = Inap::create([
					'pegawai_id' => $inputs['pegawai_id'],
					'biaya_id' => $inputs['biaya_id'],
					'hotel' => $inputs['hotel'],
					'room' => $inputs['room'],
					'harga' => $inputs['harga'] == null ? 0 : $inputs['harga'],
					'tgl_checkin' => $inputs['tgl_checkin'],
					'tgl_checkout' => $inputs['tgl_checkout'],
					'total_bayar' => $inputs['total_bayar'],
					'jml_hari' => $inputs['jml_hari'],
					'catatan' => $inputs['catatan']
				]);
		
				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('pegawai_id', $inputs['pegawai_id'])
				->first();

				$totalBiaya = $biaya->total_biaya + $inputs['total_bayar'];
				$biaya->update([
					'total_biaya_inap' => ($biaya->total_biaya_inap ?? 0) + $inputs['total_bayar'],
					'total_biaya' => $totalBiaya
				]);
		
				$results['data'] = ['total' => $totalBiaya];
			});
		
			array_push($results['messages'], 'Berhasil menambah data Penginapan.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('inap_save: '. json_encode($e->getMessage()));
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function uploadFile(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			// 'pegawai_id' 	=> 'required',
      // 'biaya_id' => 'required',
      'file' => 'required|mimes:jpeg,bmp,png,gif,pdf'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		$inap = Inap::where('id',$id)
		// ->where('pegawai_id', $inputs['pegawai_id'])
		// ->where('biaya_id', $inputs['biaya_id'])
		->first();

		$file = Utils::imageUpload($request, 'struk');
		$fileId = null;
		if($file != null) $fileId= $file->id;

		$inap->update([
			'file_id' => $fileId
		]);

    array_push($results['messages'], 'Berhasil upload struk penginapan.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function destroy($id, $biayaId, $pegawaiId)
	{
		$results = $this->responses;

		$inap = Inap::where('id',$id)
		->where('pegawai_id', $pegawaiId)
		->where('biaya_id', $biayaId)
		->first();
		
		try{
			DB::transaction(function () use ($inap, $biayaId, $pegawaiId, &$results) {
				$total_bayar = $inap->total_bayar;

				$biaya = Biaya::where('id', $biayaId)
				->where('pegawai_id', $pegawaiId)
				->first();

				$totalBiaya = $biaya->total_biaya - $total_bayar;
				$biaya->update([
					'total_biaya_inap' => $biaya->total_biaya_inap -  $total_bayar,
					'total_biaya' => $totalBiaya
				]);
				$results['data'] = ['total' => $totalBiaya];
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
