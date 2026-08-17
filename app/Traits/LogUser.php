<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait LogUser
{
    public static function bootLogUser(): void
    {
        // When creating
        static::creating(function ($table) {
            if (! auth('sanctum')->check()) {
                return;
            }
            if (Schema::hasColumn($table->getTable(), 'created_by')) {
                $table->updated_by = null;
                $table->updated_at = null;
                $table->created_by = auth('sanctum')->user()->getAuthIdentifier();
            }
        });

        // When updating
        static::updating(function ($table) {
            if (! auth('sanctum')->check()) {
                return;
            }
            if (Schema::hasColumn($table->getTable(), 'updated_by')) {
                $table->updated_by = auth('sanctum')->user()->getAuthIdentifier();
            }
        });

        // When deleting
        static::deleting(function ($table) {
            if (! auth('sanctum')->check()) {
                return;
            }
            if (Schema::hasColumn($table->getTable(), 'deleted_by')) {
                $table->deleted_by = auth('sanctum')->user()->getAuthIdentifier();
            }
        });
    }
}
