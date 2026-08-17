<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use LogUser;

    protected $table = 'satuan';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];
}
