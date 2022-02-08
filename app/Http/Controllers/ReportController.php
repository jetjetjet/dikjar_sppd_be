<?php

namespace App\Http\Controllers;

use App\Models\ReportSPPD;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Exports\SPTFinish;

use Validator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ReportController extends Controller
{
  public function reportByUser(Request $request)
	{

	}

	public function reportByPeriod(Request $request)
	{

	}

	public function reportByAnggaran(Request $request)
	{
		
	}

	public function reportByFinishedSPT(Request $request)
	{
		$results = $this->responses;
		$results['data'] = ReportSPPD::orderBy('id', 'DESC')
		->get();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function reportByPegawai(Request $request)
	{
		$results = $this->responses;
		$inputs = $request->all();
		$rules = array(
			'pegawai_id' => 'required',
			'tgl_berangkat' => 'required',
			'tgl_kembali' => 'required'
		);

		$validator = Validator::make($request->all(), $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }

		$q = SPT::join('spt_detail as sd', function($on){
			$on->on('sd.spt_id', 'spt.id');
			$on->whereNull('sd.deleted_at');
		})->join('pegawai as p', 'p.id', 'sd.pegawai_id')
		->join('biaya as b', function($on) use($inputs){
			$on->on('b.spt_id', 'spt.id');
			$on->where('b.pegawai_id', $inputs['pegawai_id']);
			$on->whereNull('b.deleted_at');
		})->where('p.id', $inputs['pegawai_id']);

		if(isset($inputs['tgl_berangkat']) && isset($inputs['tgl_kembali'])){
			$q = $q->whereRaw("tgl_berangkat::date >= '". $inputs['tgl_berangkat'] . "'::date and tgl_kembali::date <= '" . $inputs['tgl_kembali'] . "'::date");
    }

		if(isset($inputs['status'])) {
			if ($inputs['status'] == "KONSEP") {
				$q = $q->where('spt.status', "KONSEP");
			} else if ($inputs['status'] == "PROSES") {
				$q = $q->whereNotIn('spt.status', ["KONSEP"])
					->whereNull('spt.settled_at');
			} else if ($inputs['status'] == "SELESAI") {
				$q = $q->whereNotNull('spt.settled_at');
			} else {
				// surpress
			}
		}

		$results['data'] = $q->select(
			'spt.id',
			'p.full_name',
			'spt.no_spt',
			'daerah_asal as asal',
			'daerah_tujuan as tujuan',
			'tgl_berangkat',
			'tgl_kembali',
			DB::raw("to_char(b.total_biaya, 'FM999,999,999,999') as jml_biaya"),
			DB::raw("case when spt.status = 'KONSEP' then 'Draf'
				when spt.status not in ('KONSEP') and spt.completed_at is null then 'Proses'
				when spt.completed_at is not null and spt.settled_at is null then 'Kembali'
				when spt.settled_at is not null then 'Selesai' else '' end as status")
		)->get();
		
		$results['state_code'] = 200;
		$results['success'] = count($results['data']) > 0 ? true : false;
		$results['messages'] = count($results['data']) > 0 ? Array('Laporan ditemukan.') : Array('Laporan tidak ditemukan!') ;

		return response()->json($results, $results['state_code']);
	}

	public function exportFinishedSPT()
	{
		return Excel::download(new SPTFinish, 'Report_Tahunan_Perjalanan_Dinas.xlsx');
	}
}
