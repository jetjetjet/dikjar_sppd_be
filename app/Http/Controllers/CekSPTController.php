<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPTGuest;
use App\Models\SPTDetail;
use DB;
use Validator;

use Carbon\Carbon;

class CekSPTController extends Controller
{
	public function verifikasi(Request $request)
	{
		$results = $this->responses;
		
    // Validation rules.
    $rules = array(
      'key' => 'required',
		);
		
    $inputs = $request->all();
    $validator = Validator::make($inputs, $rules);

		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($respon, 400);
    }
    
    $spt = SPTGuest::join('spt', 'spt.id', 'spt_id')
		->where('key', $inputs['key'])
		->select(
			'spt.id',
			'tgl_spt',
			'jenis_dinas',
			'no_spt',
			'daerah_asal',
			'daerah_tujuan',
			DB::raw("to_char(tgl_berangkat, 'DD/MM/YYYY') as tgl_berangkat"),
			DB::raw("to_char(tgl_kembali, 'DD/MM/YYYY') as tgl_kembali"),
			'transportasi',
			DB::raw("case when status = 'PROSES' then 'Dalam perjalanan dinas'
				when status = 'KEMBALI' then 'Sudah kembali dari perjalanan dinas'
				when status = 'KWITANSI' then 'Proses kwitansi'
				when status = 'SELESAI' then 'Selesai'
				else '-' end as status")
		)->first();

		if($spt != null) {
			$data = array(
				'header' => $spt,
				'child' => array(),
				'message' => ['SPT ditemukan!'],
				'type' => 'success'
			);

			$users = SPTDetail::join('pegawai as p', 'p.id', 'spt_detail.pegawai_id')
			->where('spt_id',$spt->id)
			->select(
				'full_name', 
				'jabatan', 
				DB::raw("pangkat || ' ' || golongan as pangkat_pegawai"),
				'nip')
			->orderBy('is_pelaksana', 'DESC')
			->orderBy('nip')
			->orderBy('full_name')
			->get();

			foreach($users as $user) {
				$temp = array(
					'nama_pegawai' => $user->full_name,
					'nip' => $user->nip,
					'pangkat' => str_replace("- -","-", $user->pangkat_pegawai),
					'jabatan' => $user->jabatan
				);
				array_push($data['child'], $temp);
			}

			$results['success'] = true;
			$results['state_code'] = 200;
			$results['data'] = $data;
		} else {
			$data = array(
				'header' => $spt,
				'child' => array(),
				'message' => ['Kode tidak sesuai! SPT tidak ditemukan.'],
				'type' => 'error'
			);
			$results['state_code'] = 200;
			$results['data'] = $data;
			array_push($results['messages'], 'SPT tidak ditemukan!');
		}

    return response()->json($results, $results['state_code']);
	}
}
