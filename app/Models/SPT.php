<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogUser;

class SPT extends Model
{
	use LogUser, SoftDeletes;
	protected $table = 'spt';
	protected $fillable = [
		'no_index',
		'spt_file_id',
		'jenis_dinas',
		'bidang_id',
		'anggaran_id',
		'pelaksana_id',
		'pttd_id',
		'no_spt',
		'dasar_pelaksana',
		'untuk',
		'status',
		'transportasi',
		'periode',
		'provinsi_asal',
		'kota_asal',
		'kec_asal',
		'provinsi_tujuan',
		'kota_tujuan',
		'kec_tujuan',
		'tgl_berangkat',
		'tgl_kembali',
		'created_by',
		'updated_by',
		'deleted_at',
		'deleted_by',
		'spt_generated_at',
		'spt_generated_by',
		'finished_at',
		'finished_by'
	];
}