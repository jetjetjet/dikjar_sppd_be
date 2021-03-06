<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogUser;

class Transport extends Model
{
	use LogUser, SoftDeletes;
	protected $table = 'transport';
	protected $fillable = [
		'biaya_id',
		'pegawai_id',
		'jenis_transport',
		'catatan',
		'perjalanan',
		'agen',
		'no_tiket',
		'kode_booking',
		'no_penerbangan',
		'file_id',
		'tgl',
		'total_bayar',
		'created_by',
		'updated_by',
		'deleted_by'
	];
}