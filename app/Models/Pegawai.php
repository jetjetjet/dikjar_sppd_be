<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogUser;


class Pegawai extends Model
{
	use LogUser;
	protected $table = 'pegawai';
	protected $fillable = [
		'nip'
		,'pegawai_app'
		,'jabatan'
		,'pangkat'
		,'golongan'
		,'email'
		,'full_name'
		,'tgl_lahir'
		,'ttd'
		,'jenis_kelamin'
		,'path_foto'
		,'phone'
		,'address'
		,'active'
		,'created_by'
		,'updated_by'
	];

	public function user()
	{
		// return $this->belongsTo(User::class);
		return $this->hasOne(User::class, 'nip');
	}
}
