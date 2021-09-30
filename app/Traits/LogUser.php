<?php
namespace App\Traits;

use App\Models\User;
use DB;

trait LogUser
{
  public static function boot()
  {
    parent::boot();
    if (auth('sanctum')->check()){

      if (\Schema::hasColumn(with(new static )->getTable(), 'created_by')) {
        static::creating(function ($table) {
          $table->updated_by = null;
          $table->updated_at = null;
          $table->created_by = auth('sanctum')->user()->id;
        });
      }
      if (\Schema::hasColumn(with(new static )->getTable(), 'updated_by')) {
        static::updating(function ($table) {
          $table->updated_by = auth('sanctum')->user()->id;
        });
      }

      if (\Schema::hasColumn(with(new static )->getTable(), 'deleted_by')) {
        static::deleting(function ($table) {
          $table->deleted_by = auth('sanctum')->user()->id;
        });
      }
    }
  }
}