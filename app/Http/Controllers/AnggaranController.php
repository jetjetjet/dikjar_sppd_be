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
			})->where('periode', date('Y'));

			if(!$isAdmin){
				$datas = $datas->where('bidang', $role);
			}

			$datas = $datas->select(
				'anggaran.id',
				'kode_rekening',
				'nama_rekening',
				'uraian',
				'pagu',
				DB::raw("pagu - coalesce(realisasi,0) as sisa")
			)->get();
			foreach($datas as $dt){
				$ui = Array(
					'id' => $dt->id,
					'kode_rekening' => $dt->kode_rekening,
					'nama_rekening' => $dt->nama_rekening,
					'uraian' => $dt->uraian,
					'pagu' => 'Rp ' . number_format($dt->pagu),
					'sisa' => 'Rp ' . number_format($dt->sisa)
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
      'pejabat_pptk' => 'required',
			'bidang' => 'required',
			'bendahara' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			DB::beginTransaction();

			$anggaran = Anggaran::create([
				'kode_rekening' => $inputs['kode_rekening'],
				'nama_rekening' => $inputs['nama_rekening'],
				'bidang' => $inputs['bidang'],
				'uraian' => $inputs['uraian'],
				'pagu' => $inputs['pagu'],
				'periode' => $inputs['periode']
			]);

			$pejabat = [
				[
					'anggaran_id' => $anggaran->id,
					'pegawai_id' => $inputs['pejabat_pptk'],
					'autorisasi' => 'Pejabat Pelaksana Teknis Kegiatan',
					'autorisasi_code' => 'PPTK',
					'is_active' => '1'
				],
				[
					'anggaran_id' => $anggaran->id,
					'pegawai_id' => $inputs['bendahara'],
					'autorisasi' => 'Bendahara',
					'autorisasi_code' => 'BENDAHARA',
					'is_active' => '1'
				]
			];

			PejabatTtd::insert($pejabat);

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
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
		$q = Anggaran::join('pejabat_ttd as pt', function ($q){
			$q->on('anggaran.id', 'pt.anggaran_id');
			$q->where('pt.autorisasi_code', 'PPTK');
		})
		->join('pejabat_ttd as be', function ($q){
			$q->on('anggaran.id', 'be.anggaran_id');
			$q->where('be.autorisasi_code', 'BENDAHARA');
		})
		->where('anggaran.id', $id)
		->select('anggaran.*',
		'pt.pegawai_id as pejabat_pptk',
		'be.pegawai_id as bendahara')
		->first();

		$results['data'] = $q;

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
      'pejabat_pptk' => 'required',
			'bendahara' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
    
		try {
			DB::beginTransaction();

			$anggaran = Anggaran::find($id);
			$anggaran->update([
				'kode_rekening' => $inputs['kode_rekening'],
				'nama_rekening' => $inputs['nama_rekening'],
				'bidang' => $inputs['bidang'],
				'uraian' => $inputs['uraian'],
				'pagu' => $inputs['pagu'],
				'periode' => $inputs['periode']
			]);

			//delete missing pegawai
			PejabatTtd::where('anggaran_id', $id)
			->whereNotIn('pegawai_id', [$inputs['pejabat_pptk'], $inputs['bendahara']])
			->delete();

			$cekPptk = PejabatTtd::where('is_active', '1')
			->where('anggaran_id', $id)
			->where('pegawai_id', $inputs['pejabat_pptk'])
			->first();

			if($cekPptk == null) {
				PejabatTtd::create([
					'anggaran_id' => $id,
					'pegawai_id' => $inputs['pejabat_pptk'],
					'autorisasi' => 'Pejabat Pelaksana Teknis Kegiatan',
					'autorisasi_code' => 'PPTK',
					'is_active' => '1'
				]);
			}

			$cekBendahara = PejabatTtd::where('is_active', '1')
			->where('anggaran_id', $id)
			->where('pegawai_id', $inputs['bendahara'])
			->first();
	
			if($cekBendahara == null) {
				PejabatTtd::create([
					'anggaran_id' => $id,
					'pegawai_id' => $inputs['bendahara'],
					'autorisasi' => 'Bendahara',
					'autorisasi_code' => 'BENDAHARA',
					'is_active' => '1'
				]);
			}

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
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

		$role = Anggaran::destroy($id);
		$pejabat = PejabatTtd::where('anggaran_id', $id)->delete();

		array_push($results['messages'], 'Berhasil menghapus anggaran.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
