<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use App\Models\PejabatTtd;
use DB;
use Validator;
use Auth;

use App\Helpers\Utils;

class AnggaranController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;
		
		$role = Auth::user()->roles->pluck('name')[0] ?? null;
		$isAdmin = Auth::user()->tokenCan('is_admin') ? true : false;
		
		if ($role != null) {
			$realisasi = DB::table('spt as s')
			->join('biaya as b', 'b.spt_id', 's.id')
			->whereNull('b.deleted_at')
			->whereNull('s.deleted_at')
			->whereNotNull('s.settled_at')
			->groupBy('s.anggaran_id')
			->select('s.anggaran_id', DB::raw("sum(b.total_biaya) as realisasi"));
	
			$datas = Anggaran::where('periode', date('Y'))
			->leftJoinSub($realisasi, 'r', function ($join) {
				$join->on('anggaran.id', '=', 'r.anggaran_id');
			})->orderBy('anggaran.created_at', 'DESC');
			
			$datas = Anggaran::leftJoinSub($realisasi, 'r', function ($join) {
				$join->on('anggaran.id', '=', 'r.anggaran_id');
			})->where('periode', date('Y'));

			if(!$isAdmin){
				$datas = $datas->where('bidang', $role);
			}

			$results['data'] = $datas->select(
				'anggaran.id',
				'kode_rekening',
				'nama_rekening',
				'uraian',
				DB::raw("to_char(pagu, 'FM999,999,999,999') as pagu"),
				DB::raw("to_char(coalesce(realisasi,0), 'FM999,999,999,999') as realisasi"),
				DB::raw("to_char(pagu - coalesce(realisasi,0), 'FM999,999,999,999') as sisa"),
				'periode'
			)->get();
			
			$results['state_code'] = 200;
			$results['success'] = true;
	
		} else {
			array_push($results['messages'], 'Anggaran tidak ditemukan.');
			$results['state_code'] = 500;
			$results['success'] = false;
		}
		
		return response()->json($results, $results['state_code']);
	}

	public function searchRole(Request $request)
	{
		$results = $this->responses;

		$staf = strtoupper('staf');
		$results['data'] = DB::table('roles')->whereRaw( "UPPER(name) like '%". $staf . "%'")->select('*')->pluck('name');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		$role = Auth::user()->roles->pluck('name')[0] ?? null;
		
		$isAdmin = Auth::user()->tokenCan('is_admin') ? true : false;

		if ($role != null) {
			$realisasi = DB::table('spt as s')
			->join('biaya as b', 'b.spt_id', 's.id')
			->whereNull('b.deleted_at')
			->whereNull('s.deleted_at')
			->whereNotNull('s.settled_at')
			->groupBy('s.anggaran_id')
			->select('s.anggaran_id', DB::raw("sum(b.total_biaya) as realisasi"));
	
			$datas = Anggaran::leftJoinSub($realisasi, 'r', function ($join) {
				$join->on('anggaran.id', '=', 'r.anggaran_id');
			})->join('pegawai as bdh', 'bdh.id', 'anggaran.bendahara_id')
			->join('pegawai as pgn', 'pgn.id', 'anggaran.pengguna_id')
			->join('pegawai as pptk', 'pptk.id', 'anggaran.pptk_id')
			->where('periode', date('Y'));

			if(!$isAdmin){
				$datas = $datas->where('bidang', $role);
			}

			$datas = $datas->select(
				'anggaran.id',
				'kode_rekening',
				'nama_rekening',
				'uraian',
				'pagu',
				'pptk.full_name as pptk_name',
				'pptk.id as pptk_id',
				'bdh.full_name as bendahara_name',
				'bdh.id as bendahara_id',
				'pgn.full_name as pengguna_name',
				'pgn.id as pengguna_id',
				
				DB::raw("pagu - coalesce(realisasi,0) as sisa")
			)->get();
			foreach($datas as $dt){
				$ui = Array(
					'id' => $dt->id,
					'kode_rekening' => $dt->kode_rekening,
					'nama_rekening' => $dt->nama_rekening,
					'uraian' => $dt->uraian,
					'pagu' => 'Rp ' . number_format($dt->pagu),
					'sisa' => 'Rp ' . number_format($dt->sisa),
					'pptk_name' => $dt->pptk_name,
					'pptk_id' => $dt->pptk_id,
					'bendahara_name' => $dt->bendahara_name,
					'bendahara_id' => $dt->bendahara_id,
					'pengguna_name' => $dt->pengguna_name,
					'pengguna_id' => $dt->pengguna_id
				);
			
				array_push($results['data'], $ui);
			}
	
			$results['state_code'] = 200;
			$results['success'] = true;
		} else {
			array_push($results['messages'], 'Anggaran tidak ditemukan.');
			$results['state_code'] = 500;
			$results['success'] = false;
		}

		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$year = date('Y');
		$year1 = $year + 1;
		$inputs = $request->all();
		$rules = array(
			'kode_rekening' => 'required',
			'nama_rekening' => 'required',
      'periode' => 'required|numeric|min:'.$year.'|max:'.$year1,
      'pagu' => 'required|numeric|min:1000000|max:999999999999',
      'pptk_id' => 'required',
			'bidang' => 'required',
			'bendahara_id' => 'required',
      'pengguna_id' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			$anggaran = Anggaran::create([
				'kode_rekening' => $inputs['kode_rekening'],
				'nama_rekening' => $inputs['nama_rekening'],
				'bidang' => $inputs['bidang'],
				'uraian' => $inputs['uraian'],
				'pagu' => $inputs['pagu'],
				'periode' => $inputs['periode'],
				'bendahara_id' => $inputs['bendahara_id'],
				'pptk_id' => $inputs['pptk_id'],
				'pengguna_id' => $inputs['pengguna_id']
			]);
		} catch (\Exception $e) {
			Log::channel('spderr')->info('anggaran_save: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}

    array_push($results['messages'], 'Berhasil menambahkan Anggaran baru.');

    $results['success'] = true;
    $results['state_code'] = 201;

		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$results['data'] = Anggaran::find($id);

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function update(Request $request, $id)
	{
		$results = $this->responses;
		$year = date('Y');
		$year1 = $year + 1;
		$inputs = $request->all();
		$rules = array(
			'kode_rekening' => 'required',
			'bidang' => 'required',
			'nama_rekening' => 'required',
      'periode' => 'required|numeric|min:'.$year.'|max:'.$year1,
      'pagu' => 'required|numeric|min:1000000|max:999999999999',
      'pptk_id' => 'required',
			'bendahara_id' => 'required',
      'pengguna_id' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
    
		try {
			$anggaran = Anggaran::find($id);
			$anggaran->update([
				'kode_rekening' => $inputs['kode_rekening'],
				'nama_rekening' => $inputs['nama_rekening'],
				'bidang' => $inputs['bidang'],
				'uraian' => $inputs['uraian'],
				'pagu' => $inputs['pagu'],
				'periode' => $inputs['periode'],
				'bendahara_id' => $inputs['bendahara_id'],
				'pptk_id' => $inputs['pptk_id'],
				'pengguna_id' => $inputs['pengguna_id']
			]);

		} catch (\Exception $e) {
			Log::channel('spderr')->info('anggaran_save: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}

    array_push($results['messages'], 'Berhasil mengubah Anggaran.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;

    //Validasi
		$sptCount = DB::table('spt')->whereNull('deleted_at')->where('anggaran_id', $id)->whereNotNull('settled_at')->count();
		if($sptCount > 0) {
			array_push($results['messages'], 'Tidak dapat menghapus anggaran yang sedang berjalan!');
			return response()->json($results, $results['state_code']);
		}

		$anggaran = Anggaran::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus anggaran.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
