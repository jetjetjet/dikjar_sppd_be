<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\UnitKerja;

class UnitKerjaController extends MasterController
{
    protected function modelClass(): string
    {
        return UnitKerja::class;
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [
            'name' => ['required', 'string'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
