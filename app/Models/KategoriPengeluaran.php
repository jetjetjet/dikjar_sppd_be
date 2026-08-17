<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;

class KategoriPengeluaran extends Model
{
    use LogUser;

    protected $table = 'kat_pengeluaran';

    protected $fillable = [
        // 'bidang_id',
        'name',
        'created_by',
        'updated_by',
    ];
}
