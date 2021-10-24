<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class KategoriPengeluaran extends Model
{
	use LogUser;
	protected $table = 'kat_pengeluaran';
	protected $fillable = [
		//'bidang_id',
		'name',
		'created_by',
		'updated_by'
	];
}
