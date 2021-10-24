<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class Satuan extends Model
{
	use LogUser;
	protected $table = 'satuan';
	protected $fillable = [
		'name',
		'created_by',
		'updated_by'
	];
}
