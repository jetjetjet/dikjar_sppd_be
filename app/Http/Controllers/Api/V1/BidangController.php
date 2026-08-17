<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Bidang;

class BidangController extends MasterController
{
    protected function modelClass(): string
    {
        return Bidang::class;
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [
            'code' => ['required', 'string'],
            'name' => ['required', 'string'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
