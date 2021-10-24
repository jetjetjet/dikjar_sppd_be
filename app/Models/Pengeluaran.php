<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class Pengeluaran extends Model
{
	use LogUser;
	protected $table = 'pengeluaran';
	protected $fillable = [
		'biaya_id',
		'pegawai_id',
		'tgl',
		'kategori',
		'catatan',
		'nominal',
		'satuan',
		'jml',
		'jml_hari',
		'total',
		'total',
		'file_id',
		'created_by',
		'updated_by',
		'deleted_at',
		'deleted_by'
	];
}