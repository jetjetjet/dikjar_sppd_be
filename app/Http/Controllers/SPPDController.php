<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\Pegawai;
use App\Models\Transport;
use App\Models\Inap;
use App\Models\Biaya;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File as FaFile;
use App\Helpers\Utils;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use NcJoes\OfficeConverter\OfficeConverter;

class SPPDController extends Controller
{
  
	public function grid(Request $request, $id)
	{
		$results = $this->responses;
		$user = $request->user();
		$isAdmin = $user->tokenCan('is_admin') ? 1 : 0;
		$loginid = $user->id;
		$canGenerate = $isAdmin == 1 || $user->tokenCan('sppd-generate') ? 1 : 0;

		$header = SPT::where('id', $id)
		->select(
			'no_spt',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tgl_kembali"),
			'daerah_asal',
			'daerah_tujuan',
			'transportasi',
			'finished_at'
		)->first();
		
		$child = [];
		if ($header != null){
			$biaya = DB::table('biaya')->whereNull('deleted_at')
			->select('pegawai_id', 'spt_id', 'total_biaya');

			$child = SPTDetail::join('pegawai as p', 'p.id', 'spt_detail.pegawai_id')
			->leftJoinSub($biaya, 'biaya', function ($join) {
					$join->on('spt_detail.spt_id', 'biaya.spt_id')
					->on('spt_detail.pegawai_id', 'biaya.pegawai_id');
			})->where('spt_detail.spt_id', $id)
			->select(
				'spt_detail.id',
				'sppd_file_id',
				'p.id as pegawai_id',
				'full_name',
				'nip',
				DB::raw("coalesce(total_biaya,0) as total_biaya"),
				DB::raw("case when 1 = {$isAdmin} or p.id = {$loginid} then true else false end as can_edit")
			)->get();
		}

		$results['data']  = array( 'header' => $header, 'child' => $child);

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function getSPPD($id, $pegawaiId)
	{
		$results = $this->responses;
		$data = SPTDetail::join('files as f', 'f.id', 'sppd_file_id')
		->where('spt_detail.id', $id)
		->where('pegawai_id', $pegawaiId)
		->first();

		if($data != null){
			$results['data'] = $data->file_path . $data->file_name;
		} else {
			array_push($results['message'], 'SPPD tidak ditemukan!');
		}
		return response()->json($results, $results['state_code']);
	}

  public function show(Request $request, $id, $sptDetailId, $pegawaiId)
  {
		$results = $this->responses;

		$user = $request->user();
		$isAdmin = $user->tokenCan('is_admin') ? 1 : 0;
		$loginid = $user->id;

		$results['data'] = SPT::where('id', $id)
		->select(
			'no_spt',
			'tgl_berangkat',
			'tgl_kembali',
			DB::raw("to_char(tgl_berangkat, 'DD-MM-YYYY') as tglb_text"),
			DB::raw("to_char(tgl_kembali, 'DD-MM-YYYY') as tglk_text"),
			'daerah_asal',
			'daerah_tujuan',
			'transportasi',
			'finished_at',
			DB::raw("(select sppd_file_id from spt_detail as sd where spt.id = spt_id and deleted_at is null and pegawai_id = {$pegawaiId} ) as sppd_file_id")
		)->first();
		
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
  }
}
