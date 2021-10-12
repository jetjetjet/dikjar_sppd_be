<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogUser;

class Inap extends Model
{
	use LogUser, SoftDeletes;
	protected $table = 'inap';
	protected $fillable = [
		'biaya_id',
		'pegawai_id',
		'hotel',
		'room',
		'harga',
		'catatan',
		'tgl_checkin',
		'tgl_checkout',
		'jml_hari',
		'file_id',
		'jml_bayar',
		'created_by',
		'updated_by',
		'deleted_by',
		'checkout_at'
	];
}