<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;

class TransPesawat extends Model
{
	use LogUser;
	protected $table = 'trans_pesawat';
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
		'deleted_by'
	];
}
