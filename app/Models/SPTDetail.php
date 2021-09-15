<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class SPTDetail extends Model
{
	use LogUser;
	protected $table = 'spt_detail';
	protected $fillable = [
		'spt_id',
		'user_id',
		'created_by',
		'updated_by',
		'deleted_by'
	];
}
