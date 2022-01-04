<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SPTGuest extends Model
{
	protected $table = 'spt_guest';
	protected $fillable = [
		'spt_id',
		'key'
	];
}
