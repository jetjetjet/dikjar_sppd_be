<?php

namespace App\Models\Wilayah;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
	protected $table = 'kabupaten';
  public $timestamps = false;
  protected $fillable = ['provinsi_id', 'name'];
}
