<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use DB;

class NotifController extends Controller
{
	public function notif(Request $request)
	{
		$results = $this->responses;

		$user = $request->user();
		$isAdmin = $user->tokenCan('is_admin') ? 1 : 0;

		$q = SPT::whereNull('finished_at')
		->whereNotNull('proceed_at')
		->where('status', '!=', 'VOID')
		->whereRaw("now()::date - tgl_kembali::date > 1")
		->orderBy('tgl_kembali');

		if (!$isAdmin) {
			$loginid = auth('sanctum')->user()->id;
			$q = $q->where('spt.created_by', $loginid);
		}

		$data = $q->select(
			'id',
			'no_spt',
			DB::raw("'Telat ' || now()::date - tgl_kembali::date || ' hari' as telat")
		)
		->limit(5)
		->get();

		$results['data'] = array(
			'isNotif' => $q->count() > 0 ? true : false,
			'data' => $data
		);

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function listNotif(Request $request)
	{
		$results = $this->responses;

		$user = $request->user();
		$isAdmin = $user->tokenCan('is_admin') ? 1 : 0;

		$q = SPT::whereNull('finished_at')
				->where('status', '!=', 'VOID')
				->whereNotNull('proceed_at')
				->whereRaw("now()::date - tgl_kembali::date > 1");

		if (!$isAdmin) {
			$loginid = auth('sanctum')->user()->id;
			$q = $q->where('spt.created_by', $loginid);
		}
		
		$results['data'] = $q->select(
			'no_spt',
			'id',
			'daerah_tujuan',
			DB::raw("INITCAP(status) as status"),
			DB::raw("case when status = 'PROSES' then 'badge badge-primary'
				when status = 'KEMBALI' then 'badge badge-info' when status = 'KWITANSI' then 'badge badge-warning'
				else 'badge badge-dark' end as badge
			"),
			DB::raw("case when now()::date - tgl_kembali::date > 1 and proceed_at is not null and finished_at is null then 'Telat ' || now()::date - tgl_kembali::date || ' hari' else '' end as keterangan"),
			DB::raw("to_char(tgl_berangkat, 'DD/MM/YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD/MM/YYYY') as tgl_kembali"),
		)->orderByRaw("now()::date - tgl_kembali::date DESC")
		->get();

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
