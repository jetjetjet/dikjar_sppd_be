<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class Bidang extends Model
{
	use LogUser;
	protected $table = 'bidang';
	protected $fillable = [
		'code',
		'name',
		'remark',
		'created_by',
		'updated_by'
	];
}
