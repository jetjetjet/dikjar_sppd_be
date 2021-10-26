<?php

namespace App\Models;

class SPTGuest extends Model
{
	protected $table = 'spt_guest';
	protected $fillable = [
		'spt_id',
		'key'
	];
}
