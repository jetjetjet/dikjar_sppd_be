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
		$results['data'] = ReportSPPD::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function reportByPegawai(Request $request)
	{
		$results = $this->responses;
		$inputs = $request->all();
		$rules = array(
			'user_id' => 'required',
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
		})->join('users as u', 'u.id', 'sd.user_id')
		->join('biaya as b', function($on) use($inputs){
			$on->on('b.spt_id', 'spt.id');
			$on->where('b.user_id', $inputs['user_id']);
			$on->whereNull('b.deleted_at');
		})
		// ->join('jabatan as j', 'u.jabatan_id', 'j.id')
		->where('u.id', $inputs['user_id']);

		if(isset($inputs['tgl_berangkat']) && isset($inputs['tgl_kembali'])){
			$q = $q->whereRaw("tgl_berangkat::date >= '". $inputs['tgl_berangkat'] . "'::date and tgl_kembali::date <= '" . $inputs['tgl_kembali'] . "'::date");
    }

		if(isset($inputs['status'])) {
			if ($inputs['status'] == "DRAFT") {
				$q = $q->where('spt.status', "DRAFT");
			} else if ($inputs['status'] == "INPROGRESS") {
				$q = $q->whereNotIn('spt.status', ["DRAFT"])
					->whereNull('spt.finished_at');
			} else if ($inputs['status'] == "FINISH") {
				$q = $q->whereNotNull('spt.finished_at');
			} else {
				// surpress
			}
		}

		$results['data'] = $q->select(
			'spt.id',
			'u.full_name',
			'spt.no_spt',
			DB::raw("coalesce(kota_tujuan, kec_tujuan) as tujuan"),
			DB::raw("coalesce(kota_asal, kec_asal) as asal"),
			'tgl_berangkat',
			'tgl_kembali',
			DB::raw("to_char(b.jml_biaya, 'FM999,999,999,999') as jml_biaya"),
			DB::raw("case when spt.status = 'DRAFT' then 'Draf'
				when spt.status not in ('DRAFT') and spt.finished_at is null then 'Proses'
				when spt.finished_at is not null then 'Selesai' else '' end as status")
		)->get();
		
		$results['state_code'] = 200;
		$results['success'] = count($results['data']) > 0 ? true : false;
		$results['messages'] = count($results['data']) > 0 ? Array('Laporan ditemukan.') : Array('Laporan tidak ditemukan!') ;

		return response()->json($results, $results['state_code']);
	}

	public function exportFinishedSPT()
	{
		return Excel::download(new SPTFinish, 'tesss.xlsx');
	}
}
