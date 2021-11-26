<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use App\Models\SPT;
use DB;

class DashboardController extends Controller
{
	public function pegawaiDinas()
	{
		$results = $this->responses;
		$q = SPT::join('spt_detail as sd', function($query){
			$query->on('sd.spt_id', 'spt.id')
			->whereNull('sd.deleted_at');
		})->join('pegawai as p', 'p.id', 'sd.pegawai_id')
		->whereNull('spt.settled_at')
		->whereNull('sd.settled_at')
		->select(
			'full_name',
			'jabatan',
			'daerah_tujuan',
			DB::raw("coalesce(path_foto, '/storage/profile/user.png') as path_foto"),
			DB::raw("to_char(tgl_berangkat, 'DD/MM/YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD/MM/YYYY') as tgl_kembali"),
		)->orderBy('tgl_berangkat', 'DESC')
		->get();

		$results['data'] = $q;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function anggaran()
	{
		$results = $this->responses;

		$realisasi = DB::table('spt as s')
		->join('biaya as b', 'b.spt_id', 's.id')
		->whereNull('b.deleted_at')
		->whereNull('s.deleted_at')
		->whereNotNull('s.settled_at')
		->groupBy('s.anggaran_id')
		->select('s.anggaran_id', DB::raw("sum(b.total_biaya) as realisasi"));

		$anggaran = Anggaran::where('periode', date('Y'))
		->leftJoinSub($realisasi, 'r', function ($join) {
			$join->on('anggaran.id', '=', 'r.anggaran_id');
		})->select(
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
