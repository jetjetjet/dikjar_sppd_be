<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Helpers\Utils;

class TransportController extends Controller
{
	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Transport::find($id);
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

  public function store(Request $request)
	{
		$results = $this->responses;
		
		$inputs = $request->all();
		$rules = array(
			'pegawai_id' 	=> 'required',
      'biaya_id' => 'required',
      'jenis_transport' => 'required',
      'perjalanan' => 'required',
      'agen' => 'required',
      'no_tiket' => 'required',
      'tgl' => 'required',
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
				$transport = Transport::create([
					'pegawai_id' => $inputs['pegawai_id'],
					'biaya_id' => $inputs['biaya_id'],
					'jenis_transport' => $inputs['jenis_transport'],
					'catatan' => $inputs['catatan'],
					'perjalanan' => $inputs['perjalanan'],
					'agen' => $inputs['agen'],
					'no_tiket' => $inputs['no_tiket'],
					'kode_booking' => $inputs['kode_booking'],
					'no_penerbangan' => $inputs['no_penerbangan'],
					'tgl' => $inputs['tgl'],
					'total_bayar' => $inputs['total_bayar']
				]);
		
				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('pegawai_id', $inputs['pegawai_id'])
				->first();

				$totalBiaya = $biaya->total_biaya + $inputs['total_bayar'];
				$biaya->update([
					'total_biaya_transport' => ( $biaya->total_biaya_transport ?? 0 ) + $inputs['total_bayar'],
					'total_biaya' => $totalBiaya
				]);
		
				$results['data'] = ['total' => $totalBiaya];
			});
		
			array_push($results['messages'], 'Berhasil menambahkan Perjalanan.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('transport_save_err: '. json_encode($e->getMessage()));
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'pegawai_id' 	=> 'required',
      'biaya_id' => 'required',
      'jenis_transport' => 'required',
      'perjalanan' => 'required',
      'agen' => 'required',
      'no_tiket' => 'required',
      'tgl' => 'required',
      'total_bayar' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
		$transport = Transport::where('id',$id)
		->where('pegawai_id', $inputs['pegawai_id'])
		->where('biaya_id', $inputs['biaya_id'])
		->first();

		try{
			DB::transaction(function () use ($inputs, $transport, &$results) {

				$total_bayar = $transport->total_bayar - $inputs['total_bayar'];

				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('pegawai_id', $inputs['pegawai_id'])
				->first();

				$transport->update([
					'jenis_transport' => $inputs['jenis_transport'],
					'perjalanan' => $inputs['perjalanan'],
					'catatan' => $inputs['catatan'],
					'agen' => $inputs['agen'],
					'no_tiket' => $inputs['no_tiket'],
					'kode_booking' => $inputs['kode_booking'],
					'no_penerbangan' => $inputs['no_penerbangan'],
					'tgl' => $inputs['tgl'],
					'total_bayar' => $inputs['total_bayar']
				]);
				
				$totalBiaya = $biaya->total_biaya - ($total_bayar);
				$biaya->update([
					'total_biaya_transport' => $biaya->total_biaya_transport - ($total_bayar),
					'total_biaya' => $totalBiaya
				]);
				
				$results['data'] = ['total' => $totalBiaya];
			});

			array_push($results['messages'], 'Berhasil memperbaharui transportasi.');
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

		$transport = Transport::where('id',$id)
		// ->where('pegawai_id', $inputs['pegawai_id'])
		// ->where('biaya_id', $inputs['biaya_id'])
		->first();

		$file = Utils::imageUpload($request, 'struk');
		$fileId = null;
		if($file != null) $fileId= $file->id;

		$transport->update([
			'file_id' => $fileId
		]);
    array_push($results['messages'], 'Berhasil upload struk transportasi.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function destroy($id, $biayaId, $pegawaiId)
	{
		$results = $this->responses;

		$transport = Transport::where('id',$id)
		->where('pegawai_id', $pegawaiId)
		->where('biaya_id', $biayaId)
		->first();
		
		try{
			DB::transaction(function () use ($transport, $biayaId, $pegawaiId, &$results) {
				$total_bayar = $transport->total_bayar;
		
				$biaya = Biaya::where('id', $biayaId)
				->where('pegawai_id', $pegawaiId)
				->first();
		
				$totalBiaya = $biaya->total_biaya - $total_bayar;
				$biaya->update([
					'total_biaya_transport' => $biaya->total_biaya_transport - $total_bayar,
					'total_biaya' => $totalBiaya
				]);
				
				$results['data'] = ['total' => $totalBiaya];
				//delete
				$transport->delete();
			});
			
			array_push($results['messages'], 'Berhasil menghapus data transportasi.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('transport_delete: '. json_encode($e->getMessage()));
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}
}
