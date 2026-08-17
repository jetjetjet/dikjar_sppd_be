<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;

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
        'updated_by',
    ];
}
