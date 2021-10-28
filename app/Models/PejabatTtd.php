<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class PejabatTtd extends Model
{
	use HasFactory, LogUser;
	protected $table = 'pejabat_ttd';
	protected $fillable = [
		'pegawai_id',
		'anggaran_id',
		'autorisasi',
		'autorisasi_code',
		'is_active',
		'created_by',
		'updated_by'
	];
}
