<?php

namespace App\Models\Wilayah;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
	protected $table = 'kecamatan';
  public $timestamps = false;
  protected $fillable = ['kabupaten_id', 'name'];
}
