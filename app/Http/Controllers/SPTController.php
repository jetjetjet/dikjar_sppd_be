<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPT;
use App\Models\SPTDetail;
use App\Models\ReportSPPD;
use App\Models\User;
use App\Models\Biaya;
use App\Models\Inap;
use App\Models\Transport;
use App\Models\Jabatan;
use App\Helpers\Utils;
use Auth;

use DB;
use Validator;
use Carbon\Carbon;

use App\Helpers\MyPdf;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\File as FaFile;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use NcJoes\OfficeConverter\OfficeConverter;

class SPTController extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$users = SPTDetail::join('users as u', 'u.id', 'spt_detail.user_id')
		->groupBy('spt_detail.spt_id')
		->select(
			'spt_id',
			DB::raw("string_agg(u.full_name, '_') as name")
		);

		// $isAdmin = auth('sanctum')->user();
		if (!$request->user()->tokenCan('is_admin')) {
			$users = $users->where('u.id' , $request->user()->id);
		}

		$results['data'] = SPT::joinSub($users, 'u', function ($join) {
			$join->on('spt.id', '=', 'u.spt_id');
		})->select(
			'id',
			'no_spt',
			'jenis_dinas',
			DB::raw("tgl_berangkat || ' s/d ' || tgl_kembali as tgl"),
			DB::raw("coalesce(kota_tujuan, kec_tujuan) as tujuan"),
			'untuk',
			DB::raw("case when spt_file_id is not null then true else false end as spt_file"),
			DB::raw("'__' as nama"),
			'finished_at',
			'u.name'
		)->get();

		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function cetakSPT($id)
	{
		$results = $this->responses;
		$spt = SPT::find($id);
		
		if($spt->status == 'DRAFT'){
			$templatePath = base_path('public/storage/template/template_spt.docx');
			$checkFile = FaFile::exists($templatePath);
			if($checkFile){
				$users = SPTDetail::join('users as u', 'u.id', 'spt_detail.user_id')
				->join('jabatan as j', 'j.id', 'u.jabatan_id')
				->where('spt_id',$id)
				->select(
					DB::raw("ROW_NUMBER() OVER (ORDER BY spt_detail.id) AS index_no"), 
					'full_name as nama_pegawai', 
					'j.name as jabatan_pegawai', 
					'nip as nip_pegawai')
				->get();
	
				$userValue = array();
				foreach($users as $user){
					$temp = array(
						'index_no' => $user->index_no,
						'nama_pegawai' => $user->nama_pegawai,
						'jabatan_pegawai' => $user->jabatan_pegawai,
						'nip_pegawai' => $user->nip_pegawai
					);
					array_push($userValue, $temp);
				}
	
				$pejabat = User::join('jabatan as j', 'j.id', 'jabatan_id')
				->where('users.id', $spt->pttd_user_id)
				->select(
					'full_name',
					'nip',
					'j.name as jabatan',
					'golongan'
				)->first();
				// $jabatanKadin = Jabatan::find($kadin->jabatan_id)->first();
	
				try{
					
					$brgkt = new Carbon($spt->tgl_berangkat);
					$kembali = new Carbon($spt->tgl_kembali);
					$tglSpt = Carbon::now()->isoFormat('D MMMM Y');

					$template = new TemplateProcessor($templatePath);
	
					$template->setValue('dasar_pelaksana', $spt->dasar_pelaksana);
					$template->setValue('untuk', $spt->untuk);
					$template->setValue('tgl_berangkat', $brgkt->isoFormat('D MMMM Y'));
					$template->setValue('tgl_kembali', $kembali->isoFormat('D MMMM Y'));
					$template->setValue('tgl_cetak', $tglSpt);
					$template->setValue('nomor_surat', $spt->no_spt);
					$template->setValue('nama_pejabat', $pejabat->full_name);
					$template->setValue('nip_pejabat', $pejabat->nip);
					$template->setValue('jabatan_pejabat', strtoupper($pejabat->jabatan));
					$template->setValue('golongan_pejabat', $pejabat->golongan);
		
					$template->cloneRowAndSetValues('index_no', $userValue);
					
					$newFile = new \stdClass();
					$newFile->dbPath ='/storage/spt/';
					$newFile->ext = '.pdf';
					$newFile->originalName = "SPT_Generated";
					$newFile->newName = time()."_".$newFile->originalName;

					$path = base_path('/public');
					$template->saveAs($path . $newFile->dbPath . $newFile->newName . ".docx", TRUE);
					//Convert kwe PDF
					$docPath = $path . $newFile->dbPath . $newFile->newName . ".docx";
          $converter = new OfficeConverter($docPath);
          //generates pdf file in same directory as test-file.docx
          $converter->convertTo($newFile->newName.".pdf");

					$newFile->newName = $newFile->newName.".pdf";

					$file = Utils::saveFile($newFile);
					// update
					$spt->update([
						'spt_file_id' => $file,
						'spt_generated_at' => DB::raw("now()"),
						'spt_generated_by' => auth('sanctum')->user()->id,
						'status' => 'SPTGENERATED'
					]);
					
					array_push($results['messages'], 'Berhasil membuat SPT.');
					$results['success'] = true;
					$results['state_code'] = 200;
				} catch (\Exception $e) {
					Log::channel('spderr')->info('spt_cetak_err: '. json_encode($e->getMessage()));
					array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
				}
	
			} else {
				array_push($results['messages'], 'Template SPT tidak ditemukan.');
			}
		} else {
			array_push($results['messages'], 'Berkas dan Nomor SPT sudah pernah dibuat.');
		}
		
		return response()->json($results, $results['state_code']);
	}

	public function store(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
      'anggaran_id' => 'required',
			'jenis_dinas'=> 'required', 
      'pttd_user_id' => 'required',
      'dasar_pelaksana' => 'required',
      'untuk' => 'required',
      'transportasi' => 'required',
      // 'provinsi_asal' => 'required',
      // 'kota_asal' => 'required',
      // 'provinsi_tujuan' => 'required',
      // 'kota_tujuan' => 'required',
      'tgl_berangkat' => 'required',
      'tgl_kembali' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			DB::transaction(function () use ($inputs) {
				$noMax = SPT::max('no_index') + 1 ?? 1;
				$tahun = Carbon::now()->format('Y');
				$noSpt = '090/'. str_pad($noMax, 3, '0', STR_PAD_LEFT) . '/SPPD/PDK/' . $tahun ;
				$spt = SPT::create([
					'no_index' => $noMax,
					'no_spt' => $noSpt,
					// 'bidang_id' => $inputs['bidang_id'],
					'jenis_dinas' => $inputs['jenis_dinas'],
					'anggaran_id' => $inputs['anggaran_id'],
					'pttd_user_id' => $inputs['pttd_user_id'],
					'dasar_pelaksana' => $inputs['dasar_pelaksana'],
					'untuk' => $inputs['untuk'],
					'transportasi' => $inputs['transportasi'],
					'provinsi_asal' => $inputs['provinsi_asal'],
					'kota_asal' => $inputs['kota_asal'],
					'kec_asal' => $inputs['kec_asal'],
					'provinsi_tujuan' => $inputs['provinsi_tujuan'],
					'kota_tujuan' => $inputs['kota_tujuan'],
					'kec_tujuan' => $inputs['kec_tujuan'],
					'tgl_berangkat' => $inputs['tgl_berangkat'],
					'tgl_kembali' => $inputs['tgl_kembali'],
					'status' => 'DRAFT',
					'periode' => '2021'
				]);

				foreach($inputs['user_id'] as $userid){
					$detail = SPTDetail::create([
						'spt_id' => $spt->id,
						'user_id' => $userid
					]);
				}
			});
	
			array_push($results['messages'], 'Berhasil menambahkan SPT baru.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e) {
			Log::channel('spderr')->info('spt_save_err: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}

		return response()->json($results, $results['state_code']);
	}

	public function finish($id)
	{
		$results = $this->responses;
		try {
			DB::transaction(function () use ($id, &$results) {
				$spt = SPT::where('id', $id)->whereNull('spt_generated_at');
				$sppd = SPTDetail::where('spt_id', $id)->whereNull('sppd_generated_at');
		
				if($spt->count() < 1 && $sppd->count() < 1) {
					$loginId = auth('sanctum')->user()->id;
					$finish = array(
						'finished_at' => DB::raw("now()"),
						'finished_by' => $loginId
					);
					$spt = SPT::where('id', $id)->first();
					$sppd = SPTDetail::where('spt_id', $id)->get();

					foreach($sppd as $dtl) {
						$biaya = Biaya::where('spt_id', $id)
						->where('user_id', $dtl->user_id)->first();

						$userJbtn = User::join('jabatan as j', 'j.id', 'jabatan_id')
						->where('users.id', $dtl->user_id)
						->select(
							'full_name',
							//'nip',
							'j.name as jabatan'
							//'golongan'
						)->first();

						$inap = Inap::where('biaya_id', $biaya->id)
						->where('user_id', $dtl->user_id)->first();

						$pesawatBrgkt = Transport::where('biaya_id', $biaya->id)
						->where('user_id', $dtl->user_id)
						->where('perjalanan', 'Berangkat')
						->where('jenis_transport', 'Pesawat')
						->first();

						$pesawatPlg = Transport::where('biaya_id', $biaya->id)
						->where('user_id', $dtl->user_id)
						->where('perjalanan', 'Pulang')
						->where('jenis_transport', 'Pesawat')
						->first();

						$asal = $spt->kota_asal != null ? ucwords(strtolower($spt->kota_asal)) : ucwords(strtolower($spt->kec_asal));
						$tujuan = $spt->kota_tujuan != null ? ucwords(strtolower($spt->kota_tujuan)) : ucwords(strtolower($spt->kec_tujuan));
						$checkin = $inap->tgl_checkin ?? null;
						$checkout = $inap->tgl_checkout ?? null;
						$pesbrgkt_tgl = $pesawatBrgkt->tgl ?? null;
						$peskmbl_tgl = $pesawatPlg->tgl ?? null;

						$report = ReportSPPD::insert([
							'user_id' => $dtl->user_id,
							'spt_id' => $spt->id,
							'spt_detail_id' => $dtl->id,
							'biaya_id' => $biaya->id,
							'nama_pelaksana' => $userJbtn->full_name,
							'jabatan' => $userJbtn->jabatan,
							'no_pku' => null,
							'no_spt' => $spt->no_spt,
							'no_sppd' => null,
							'kegiatan' => $spt->untuk,
							'penyelenggara' => 'SD Dalam Kab. Kerinci',
							'lok_asal'=> $asal,
							'lok_tujuan' => $tujuan,
							'tgl_berangkat' => $spt->tgl_berangkat,
							'tgl_kembali' => $spt->tgl_kembali,
							'uang_saku' => $biaya->uang_saku ?? null,
							'uang_makan' => $biaya->uang_makan ?? null,
							'uang_representasi' => $biaya->uang_representasi ?? null,
							'uang_penginapan'  => $biaya->uang_inap ?? null,
							'uang_travel' => $biaya->uang_travel ?? null,
							'uang_total' => $biaya->jml_biaya ?? null,
							'uang_pesawat' => $biaya->uang_pesawat ?? null,
							'inap_hotel' => $inap->hotel ?? null,
							'inap_room' => $inap->room ?? null,
							'inap_checkin' => $checkin,
							'inap_checkout' => $checkout,
							'inap_jml_hari' => $inap->jml_hari ?? null,
							'inap_per_malam' => $inap->harga ?? null,
							'inap_jumlah' => $inap->jml_bayar ?? null,
							'pesbrgkt_maskapai' => $pesawatBrgkt->agen ?? null,
							'pesbrgkt_no_tiket' => $pesawatBrgkt->no_tiket ?? null,
							'pesbrgkt_kode_booking' => $pesawatBrgkt->kode_booking ?? null,
							'pesbrgkt_no_penerbangan' => $pesawatBrgkt->no_penerbangan ?? null,
							'pesbrgkt_tgl' => $pesbrgkt_tgl,
							'pesbrgkt_jumlah' => $pesawatBrgkt->jml_bayar ?? null,
							'peskmbl_maskapai' => $pesawatPlg->agen ?? null,
							'peskmbl_no_tiket' => $pesawatPlg->agen ?? null,
							'peskmbl_kode_booking' => $pesawatPlg->agen ?? null,
							'peskmbl_no_penerbangan' => $pesawatPlg->agen ?? null,
							'peskmbl_tgl' => $peskmbl_tgl,
							'peskmbl_jumlah' => $pesawatPlg->jml_bayar ?? null
						]);
						
					}

					$spt->update($finish);
					SPTDetail::where('spt_id', $id)->update($finish);
		
					array_push($results['messages'], 'Perjalanan Dinas berhasil diselesaikan.');
					$results['state_code'] = 200;
					$results['success'] = true;
				} else {
					array_push($results['messages'], 'SPT tidak dapat diselesaikan! SPT/SPPD belum dibuat.');
				}
			});
		}
		catch (\Exception $e) {
			Log::channel('spderr')->info('spt_finish: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}
		
		return response()->json($results, $results['state_code']);
	}

	public function show($id)
	{
		$results = $this->responses;
		$data = SPT::join('anggaran as ag', 'ag.id', 'anggaran_id')
		->where('spt.id', $id)
		->select(
			'spt.*',
			'ag.mak as anggaran_text'
		)->first();

		$data->user_id = SPTDetail::where('spt_id', $id)->get()->pluck('user_id');
		$results['data'] = $data;

		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function getSPT($id)
	{
		$results = $this->responses;
		$data = SPT::join('files as f', 'f.id', 'spt_file_id')
		->where('spt.id', $id)
		->first();

		if($data != null){
			$results['data'] = $data->file_path . $data->file_name;
		} else {
			array_push($results['message'], 'SPT tidak ditemukan!');
		}
		return response()->json($results, $results['state_code']);
	}
	

	public function update(Request $request, $id)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			// 'bidang_id' => 'required',
			'jenis_dinas' => 'required',
      'anggaran_id' => 'required',
      'pttd_user_id' => 'required',
      'dasar_pelaksana' => 'required',
      'untuk' => 'required',
      'transportasi' => 'required',
      'pttd_user_id' => 'required',
      // 'provinsi_asal' => 'required',
      // 'kota_asal' => 'required',
      // 'provinsi_tujuan' => 'required',
      // 'kota_tujuan' => 'required',
      'tgl_berangkat' => 'required',
      'tgl_kembali' => 'required'
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, 200);
    }

		try {
			DB::transaction(function () use ($inputs, $id) {
				$spt = SPT::find($id);
				
				$updateSpt = $spt->update([
					// 'bidang_id' => $inputs['bidang_id'],
					'jenis_dinas' => $inputs['jenis_dinas'],
					'anggaran_id' => $inputs['anggaran_id'],
					'pttd_user_id' => $inputs['pttd_user_id'],
					'dasar_pelaksana' => $inputs['dasar_pelaksana'],
					'untuk' => $inputs['untuk'],
					'transportasi' => $inputs['transportasi'],
					'provinsi_asal' => $inputs['provinsi_asal'],
					'kota_asal' => $inputs['kota_asal'],
					'kec_asal' => $inputs['kec_asal'],
					'provinsi_tujuan' => $inputs['provinsi_tujuan'],
					'kota_tujuan' => $inputs['kota_tujuan'],
					'kec_tujuan' => $inputs['kec_tujuan'],
					'tgl_berangkat' => $inputs['tgl_berangkat'],
					'tgl_kembali' => $inputs['tgl_kembali']
				]);

				//Delete Missing User Id
				$data = SPTDetail::where('spt_id', $id)
				->whereNotIn('id', $inputs['user_id'])
				->delete();

				//Insert new or skip
				foreach($inputs['user_id'] as $userid){
					$detailSPT = SPTDetail::where('user_id', $userid)->where('spt_id', $id)->first();
					if ($detailSPT == null ){
						$detail = SPTDetail::create([
							'spt_id' => $id,
							'user_id' => $userid
						]);
					}
				}
			});
	
			array_push($results['messages'], 'Berhasil mengubah SPT.');
			$results['success'] = true;
			$results['state_code'] = 200;
		} catch(\Exception $e){
			Log::channel('spderr')->info('spt_update_err: '. json_encode($e->getMessage()));
			array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
		}

		return response()->json($results, $results['state_code']);
	}
  
	public function destroy($id)
	{
		$results = $this->responses;
		$header = SPT::find($id);
		if ($header->status == 'DRAFT'){
			try{
				DB::transaction(function () use ($id) {
					$detail = SPTDetail::where('spt_id', $id)->delete();
					$header = SPT::find($id)->delete();
				});
	
				array_push($results['messages'], 'Berhasil menghapus data.');
				$results['state_code'] = 200;
				$results['success'] = true;
			} catch(\Exception $e){
				Log::channel('spderr')->info('spt_delete_err: '. json_encode($e->getMessage()));
				array_push($results['messages'], 'Kesalahan! Tidak dapat memproses.');
			}
		} else {
			array_push($results['messages'], 'SPT tidak dapat dihapus!');
		}

		return response()->json($results, $results['state_code']);
	}
}
