<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;

class Penginapan extends Model
{
    use LogUser;

    protected $table = 'inap';

    protected $fillable = [
        'spt_id',
        'hotel',
        'room',
        'tgl_checkin',
        'tgl_checkout',
        'jml_hari',
        'jml_bayar',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
