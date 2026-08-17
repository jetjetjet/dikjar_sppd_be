<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\KategoriPengeluaran;

class KategoriPengeluaranController extends MasterController
{
    protected function modelClass(): string
    {
        return KategoriPengeluaran::class;
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}
