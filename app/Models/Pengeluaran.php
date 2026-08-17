<?php

namespace App\Models;

use App\Traits\LogUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    use LogUser;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'biaya_id',
        'pegawai_id',
        'tgl',
        'kategori',
        'catatan',
        'nominal',
        'satuan',
        'jml',
        'jml_hari',
        'total',
        'total',
        'file_id',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function biaya(): BelongsTo
    {
        return $this->belongsTo(Biaya::class);
    }
}
