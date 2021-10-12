<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransportController extends Controller
{
	public function grid($biayaId, $pegawaiId)
	{
		$results = $this->responses;
		$results['data'] = Transport::where('biaya_id', $biayaId)
		->where('pegawai_id', $pegawaiId)
		->select(
			'id',
			'jenis_transport',
			DB::raw("to_char(tgl, 'DD-MM-YYYY') as tgl_text"),
			'perjalanan',
			'agen',
			'no_tiket',
			DB::raw("coalesce(kode_booking,'-') as kode_booking"),
			'no_penerbangan',
			'tgl',
			'jml_bayar',
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
			'pegawai_id' 	=> 'required',
      'biaya_id' => 'required',
      'jenis_transport' => 'required',
      'perjalanan' => 'required',
      'agen' => 'required',
      'no_tiket' => 'required',
      'tgl' => 'required',
      'jml_bayar' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try{
			DB::transaction(function () use ($inputs, $results) {
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
					'jml_bayar' => $inputs['jml_bayar']
				]);
		
				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('pegawai_id', $inputs['pegawai_id'])
				->first();

				if($inputs['jenis_transport'] == 'Travel' || $inputs['jenis_transport'] == 'Taksi'){
					$biaya->update([
						'uang_travel' => ($biaya->uang_travel ?? 0) + $inputs['jml_bayar'],
						'jml_biaya' => $biaya->jml_biaya + $inputs['jml_bayar']
					]);
				}

				if($inputs['jenis_transport'] == 'Pesawat') {
					$biaya->update([
						'uang_pesawat' => ( $biaya->uang_pesawat ?? 0 ) +  $inputs['jml_bayar'],
						'jml_biaya' => $biaya->jml_biaya + $inputs['jml_bayar']
					]);
				}
		
			});
		
			array_push($results['messages'], 'Berhasil menambahkan transportasi.');
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
      'jml_bayar' => 'required'
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
			DB::transaction(function () use ($inputs, $transport) {

				$jml_bayar = $transport->jml_bayar - $inputs['jml_bayar'];
				$jenis_transport = $transport->jenis_transport;

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
					'jml_bayar' => $inputs['jml_bayar']
				]);
		
				if($jenis_transport == 'Travel' || $jenis_transport == 'Taksi'){
					$biaya->update([
						'uang_travel' => $biaya->uang_travel - ($jml_bayar),
						'jml_biaya' => $biaya->jml_biaya - ($jml_bayar)
					]);
				}
		
				if($jenis_transport == 'Pesawat') {
					$biaya->update([
						'uang_pesawat' => $biaya->uang_pesawat -  ($jml_bayar),
						'jml_biaya' => $biaya->jml_biaya - ($jml_bayar)
					]);
				}
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
			'pegawai_id' 	=> 'required',
      'biaya_id' => 'required',
      'file' => 'required'
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

		$transport->update([
			'file_id' => $inputs['tgl_checkout']
		]);
    array_push($results['messages'], 'Berhasil memperbaharui data transportasi.');

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
			DB::transaction(function () use ($transport, $biayaId, $pegawaiId) {
				$jml_bayar = $transport->jml_bayar;
				$jenis_transport = $transport->jenis_transport;
		
				$biaya = Biaya::where('id', $biayaId)
				->where('pegawai_id', $pegawaiId)
				->first();
		
				if($jenis_transport == 'Travel' || $jenis_transport == 'Taksi'){
					$biaya->update([
						'uang_travel' => $biaya->uang_travel - $jml_bayar,
						'jml_biaya' => $biaya->jml_biaya - $jml_bayar
					]);
				}
		
				if($jenis_transport == 'Pesawat') {
					$biaya->update([
						'uang_pesawat' => $biaya->uang_pesawat -  $jml_bayar,
						'jml_biaya' => $biaya->jml_biaya - $jml_bayar
					]);
				}
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
