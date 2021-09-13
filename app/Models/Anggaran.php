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
		'mak',
		'uraian',
		'pagu',
		'periode',
		'created_by',
		'updated_by',
		'deleted_by'
	];
}