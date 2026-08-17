<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SPTDetail extends Model
{
    use LogUser, SoftDeletes;

    protected $table = 'spt_detail';

    protected $fillable = [
        'spt_id',
        'pegawai_id',
        'is_pelaksana',
        'sppd_file_id',
        'rumming_file_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'sppd_generated_at',
        'sppd_generated_by',
        'settled_at',
        'settled_by',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function spt()
    {
        return $this->belongsTo(SPT::class, 'spt_id');
    }
}
