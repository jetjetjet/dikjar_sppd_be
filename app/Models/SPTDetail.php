<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogUser;

class SPTDetail extends Model
{
	use LogUser, SoftDeletes;
	protected $table = 'spt_detail';
	protected $fillable = [
		'spt_id',
		'pegawai_id',
		'is_pelaksana',
		'sppd_file_id',
		'created_by',
		'updated_by',
		'deleted_by',
		'sppd_generated_at',
		'sppd_generated_by',
		'finished_at',
		'finished_by'
	];
}
