<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class JenisTransport extends Model
{
  use LogUser;
	protected $table = 'jenis_transport';
	protected $fillable = [
		//'bidang_id',
    'name',
		'created_by',
		'updated_by'
	];
}
