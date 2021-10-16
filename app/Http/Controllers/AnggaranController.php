<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use DB;
use Validator;

use App\Helpers\Utils;

class AnggaranController extends Controller
{
	public function grid(Request $request)
	{
		$results = $this->responses;
		$realisasi = DB::table('spt as s')
		->join('biaya as b', 'b.spt_id', 's.id')
		->whereNull('b.deleted_at')
		->whereNull('s.deleted_at')
		->whereNotNull('s.finished_at')
		->groupBy('s.anggaran_id')
		->select('s.anggaran_id', DB::raw("sum(b.total_biaya) as realisasi"));

		$results['data'] = Anggaran::where('periode', date('Y'))
		->leftJoinSub($realisasi, 'r', function ($join) {
			$join->on('anggaran.id', '=', 'r.anggaran_id');
		})->select(
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

		return response()->json($results, $results['state_code']);
	}

	public function search(Request $request)
	{
		$results = $this->responses;
		
		$realisasi = DB::table('spt as s')
		->join('biaya as b', 'b.spt_id', 's.id')
		->whereNull('b.deleted_at')
		->whereNull('s.deleted_at')
		->whereNotNull('s.finished_at')
		->groupBy('s.anggaran_id')
		->select('s.anggaran_id', DB::raw("sum(b.total_biaya) as realisasi"));

		$datas = Anggaran::leftJoinSub($realisasi, 'r', function ($join) {
			$join->on('anggaran.id', '=', 'r.anggaran_id');
		})->where('periode', date('Y'))
		->select(
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

		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'kode_rekening' => 'required',
			'nama_rekening' => 'required',
      'uraian' => 'required',
      'pagu' => 'required|numeric|min:4'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }
		
    Anggaran::create([
      'kode_rekening' => $inputs['kode_rekening'],
      'nama_rekening' => $inputs['nama_rekening'],
      'uraian' => $inputs['uraian'],
      'pagu' => $inputs['pagu'],
      'periode' => date('Y')
    ]);

    array_push($results['messages'], 'Berhasil menambahkan Anggaran baru.');

    $results['success'] = true;
    $results['state_code'] = 200;

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

		$inputs = $request->all();
		$rules = array(
			'kode_rekening' => 'required',
			'nama_rekening' => 'required',
      'uraian' => 'required',
      'pagu' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
    
		$anggaran = Anggaran::find($id);
    $anggaran->update([
      'kode_rekening' => $inputs['kode_rekening'],
      'nama_rekening' => $inputs['nama_rekening'],
      'uraian' => $inputs['uraian'],
      'pagu' => $inputs['pagu'],
    ]);

    array_push($results['messages'], 'Berhasil mengubah Anggaran.');

    $results['success'] = true;
    $results['state_code'] = 200;

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;

    //Validasi
		$sptCount = DB::table('spt')->whereNull('deleted_at')->where('anggaran_id', $id)->whereNotNull('finished_at')->count();
		if($sptCount > 0) {
			array_push($results['messages'], 'Tidak dapat menghapus anggaran yang sedang berjalan!');
			return response()->json($results, $results['state_code']);
		}

		$role = Anggaran::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus anggaran.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
