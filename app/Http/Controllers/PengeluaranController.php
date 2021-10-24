<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PengeluaranController extends Controller
{
	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Pengeluaran::find($id);
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
      'tgl' => 'required',
      'kategori' => 'required',
      'nominal' => 'required',
      'satuan' => 'required',
      'jml' => 'required',
      'total' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try{
			DB::transaction(function () use ($inputs, &$results) {
				$pengeluaran = Pengeluaran::create([
					'biaya_id' => $inputs['biaya_id'],
					'pegawai_id' => $inputs['pegawai_id'],
					'tgl' => $inputs['tgl'],
					'kategori' => $inputs['kategori'],
					'catatan' => $inputs['catatan'],
					'nominal' => $inputs['nominal'],
					'satuan' => $inputs['satuan'],
					'jml' => $inputs['jml'],
					'jml_hari' => $inputs['jml_hari'] ?? null,
					'total' => $inputs['total']
				]);
		
				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('pegawai_id', $inputs['pegawai_id'])
				->first();

				$totalBiaya = $biaya->total_biaya + $inputs['total'];
				$biaya->update([
					'total_biaya_lainnya' => ( $biaya->total_biaya_lainnya ?? 0 ) +  $inputs['total'],
					'total_biaya' => $totalBiaya
				]);
				$results['data'] = ['total' => $totalBiaya];
			});
		
			array_push($results['messages'], 'Berhasil menambahkan Pengeluaran.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('pengeluaran_save: '. json_encode($e->getMessage()));
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
      'tgl' => 'required',
      'kategori' => 'required',
      'nominal' => 'required',
      'satuan' => 'required',
      'jml' => 'required',
      'total' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
		$pengeluaran = Pengeluaran::where('id',$id)
		->where('pegawai_id', $inputs['pegawai_id'])
		->where('biaya_id', $inputs['biaya_id'])
		->first();

		try{
			DB::transaction(function () use ($inputs, $pengeluaran, &$results) {

				$total_biaya = $pengeluaran->total - $inputs['total'];

				$biaya = Biaya::where('id', $inputs['biaya_id'])
				->where('pegawai_id', $inputs['pegawai_id'])
				->first();

				$pengeluaran->update([
					'tgl' => $inputs['tgl'],
					'kategori' => $inputs['kategori'],
					'catatan' => $inputs['catatan'],
					'nominal' => $inputs['nominal'],
					'satuan' => $inputs['satuan'],
					'jml' => $inputs['jml'],
					'jml_hari' => $inputs['jml_hari'] ?? null,
					'total' => $inputs['total']
				]);

				$totalBiaya = $biaya->total_biaya - ($total_biaya);
				$biaya->update([
					'total_biaya_lainnya' => $biaya->total_biaya_lainnya - ($total_biaya),
					'total_biaya' => $totalBiaya
				]);
				
				$results['data'] = ['total' => $totalBiaya];
			});

			array_push($results['messages'], 'Berhasil memperbaharui Pengeluaran.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('pengeluaran_update: '. json_encode($e->getMessage()));
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

		$pengeluaran = Pengeluaran::where('id',$id)
		->where('pegawai_id', $inputs['pegawai_id'])
		->where('biaya_id', $inputs['biaya_id'])
		->first();

		$pengeluaran->update([
			'file_id' => $inputs['tgl_checkout']
		]);
    array_push($results['messages'], 'Berhasil memperbaharui data Pengeluaran.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}

	public function destroy($id, $biayaId, $pegawaiId)
	{
		$results = $this->responses;

		$pengeluaran = Pengeluaran::where('id',$id)
		->where('pegawai_id', $pegawaiId)
		->where('biaya_id', $biayaId)
		->first();
		
		try{
			DB::transaction(function () use ($pengeluaran, $biayaId, $pegawaiId, &$results) {
				$total_bayar = $pengeluaran->total;
		
				$biaya = Biaya::where('id', $biayaId)
				->where('pegawai_id', $pegawaiId)
				->first();
				
				$totalBiaya = $biaya->total_biaya - $total_bayar;
				$biaya->update([
					'total_biaya_lainnya' => $biaya->total_biaya_lainnya - $total_bayar,
					'total_biaya' => $totalBiaya
				]);
				$results['data'] = ['total' => $totalBiaya];

				//delete
				$pengeluaran->delete();
			});
			
			array_push($results['messages'], 'Berhasil menghapus data Pengeluaran.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('pengeluaran_delete: '. json_encode($e->getMessage()));
			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}
}
