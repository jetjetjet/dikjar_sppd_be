<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogUser;

class Anggaran extends Model
{
	use LogUser, SoftDeletes;
	protected $table = 'anggaran';
	protected $fillable = [
		'kode_rekening',
		'bidang',
		'nama_rekening',
		'uraian',
		'pagu',
		'periode',
		'bendahara_id',
		'pptk_id',
		'pengguna_id',
		'created_by',
		'updated_by',
		'deleted_by'
	];
}