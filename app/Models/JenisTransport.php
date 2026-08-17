<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;

class JenisTransport extends Model
{
    use LogUser;

    protected $table = 'kategori_transport';

    protected $fillable = [
        // 'bidang_id',
        'name',
        'created_by',
        'updated_by',
    ];
}
