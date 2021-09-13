<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class File extends Model
{
	use LogUser;
	protected $fillable = [
		'file_name',
    'file_path',
    'original_name',
		'ext',
    'created_at',
    'created_by',
    'updated_at',
    'updated_by'
  ];
}
