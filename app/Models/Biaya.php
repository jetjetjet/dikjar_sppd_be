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
		'user_id',
		'uang_makan',
		'uang_saku',
		'uang_representasi',
		'uang_inap',
		'uang_travel',
		'uang_pesawat',
		'jml_biaya',
		'created_by',
		'updated_by',
		'deleted_by'
	];
}