<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class Jabatan extends Model
{
  use LogUser;
	protected $table = 'jabatan';
	protected $fillable = [
		//'bidang_id',
    'name',
    'remark',
		'role_name',
		'is_parent',
		'parent_id',
		'created_by',
		'updated_by',
		'deleted_by'
	];
}