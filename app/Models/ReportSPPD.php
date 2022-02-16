<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class ReportSPPD extends Model
{
	use LogUser;
	protected $table = 'report_sppd';
	protected $fillable = [
		'pegawai_id',
		'spt_id',
		'spt_detail_id',
		'biaya_id',
		'nama_pelaksana',
		'jabatan',
		'no_pku',
		'no_spt',
		'no_sppd',
		'jml_hari',
		'kegiatan',
		'penyelenggara',
		'lok_asal',
		'lok_tujuan',
		'tgl_berangkat',
		'tgl_kembali',
		'uang_saku',
		'uang_makan',
		'uang_representasi',
		'uang_penginapan',
		'uang_transport',
		'uang_lain',
		'uang_pesawat',
		'uang_total',
		'inap_hotel',
		'inap_room',
		'inap_checkin',
		'inap_checkout',
		'inap_jml_hari',
		'inap_per_malam',
		'inap_jumlah',
		'pesbrgkt_maskapai',
		'pesbrgkt_no_tiket',
		'pesbrgkt_kode_booking',
		'pesbrgkt_no_penerbangan',
		'pesbrgkt_tgl',
		'pesbrgkt_jumlah',
		'peskmbl_maskapai',
		'peskmbl_no_tiket',
		'peskmbl_kode_booking',
		'peskmbl_no_penerbangan',
		'peskmbl_tgl',
		'peskmbl_jumlah',
		'uang_dinas_dlm',
		'uang_dinas_luar',
		'nama_rekening',
		'kode_rekening'
	];
}