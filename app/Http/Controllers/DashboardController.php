<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use App\Models\SPT;
use Auth;
use DB;

class DashboardController extends Controller
{
	public function pegawaiDinas()
	{
		$results = $this->responses;
		
		$role = Auth::user()->roles->pluck('name')[0] ?? null;
		$isAdmin = Auth::user()->tokenCan('is_admin') ? true : false;

		$q = SPT::join('spt_detail as sd', function($query){
			$query->on('sd.spt_id', 'spt.id')
			->whereNull('sd.deleted_at');
		})->join('pegawai as p', 'p.id', 'sd.pegawai_id')
		->whereNotNull('spt.proceed_at')
		->whereNull('spt.voided_at')
		->whereNull('spt.settled_at')
		->whereNull('sd.settled_at');

		$datas = Anggaran::joinSub($q, 'r', function ($join) {
			$join->on('anggaran.id', '=', 'r.anggaran_id');
		})->where('anggaran.periode', date('Y'));

		if(!$isAdmin){
			$datas = $datas->where('bidang', $role);
		}

		$get = $datas->select(
			'no_spt',
			'r.spt_id',
			'r.full_name',
			'r.jabatan',
			'r.daerah_tujuan',
			DB::raw("INITCAP(status) as status"),
			DB::raw("case when status = 'KONSEP' then 'badge badge-secondary' when status = 'PROSES' then 'badge badge-primary'
				when status = 'KEMBALI' then 'badge badge-info' when status = 'KWITANSI' then 'badge badge-warning'
				when status = 'SELESAI' then 'badge badge-success' else 'badge badge-dark' end as badge
			"),
			DB::raw("case when now()::date - tgl_kembali::date > 1 and proceed_at is not null and finished_at is null then 'Telat ' || now()::date - tgl_kembali::date || ' hari' else '' end as keterangan"),
			DB::raw("coalesce(path_foto, '/storage/profile/user.png') as path_foto"),
			DB::raw("to_char(tgl_berangkat, 'DD/MM/YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD/MM/YYYY') as tgl_kembali"),
		)->orderBy('no_spt', 'DESC')
		->get();

		$results['data'] = $get;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function anggaran()
	{
		$results = $this->responses;
		$role = Auth::user()->roles->pluck('name')[0] ?? null;
		$isAdmin = Auth::user()->tokenCan('is_admin') ? true : false;

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

		$anggaran = $datas->select(
			'anggaran.id',
			'kode_rekening',
			'nama_rekening',
			'uraian',
			'pagu',
			'realisasi',
			'periode'
		)->get();

		$temp = array(
			'label' => array(),
			'anggaran' => array(),
			'realisasi' => array()
		);

		foreach($anggaran as $ang) {
			array_push($temp['label'], $ang->nama_rekening); 
			array_push($temp['anggaran'], $ang->pagu);
			array_push($temp['realisasi'], $ang->realisasi);
		}

		$results['data'] = $temp;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
