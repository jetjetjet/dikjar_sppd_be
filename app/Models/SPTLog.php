<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SPTLog extends Model
{
	protected $table = 'spt_log';
	protected $fillable = [
		'user_id',
		'username',
        'reference_id',
		'aksi',
		'success',
		'detail'
	];
}