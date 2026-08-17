<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Satuan;

class SatuanController extends MasterController
{
    protected function modelClass(): string
    {
        return Satuan::class;
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}
