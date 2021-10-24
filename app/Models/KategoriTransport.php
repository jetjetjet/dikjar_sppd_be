<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class KategoriTransport extends Model
{
  use LogUser;
	protected $table = 'kategori_transport';
	protected $fillable = [
		//'bidang_id',
    'name',
		'created_by',
		'updated_by'
	];
}
