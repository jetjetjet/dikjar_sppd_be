<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use LogUser;

    protected $table = 'bidang';

    protected $fillable = [
        'code',
        'name',
        'remark',
        'created_by',
        'updated_by',
    ];
}
