<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogUser;

class Biaya extends Model
{
	use LogUser, SoftDeletes;
	protected $table = 'biaya';
	protected $fillable = [
		'spt_id',
		'pegawai_id',
		'total_biaya_lainnya',
		'total_biaya_inap',
		'total_biaya_travel',
		'total_biaya_pesawat',
		'total_biaya',
		'uang_pesawat',
		'jml_biaya',
		'created_by',
		'updated_by',
		'deleted_by'
	];
}