<?php

namespace App\Models\Wilayah;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
	protected $table = 'provinsi';
  public $timestamps = false;
  protected $fillable = ['name'];
}
