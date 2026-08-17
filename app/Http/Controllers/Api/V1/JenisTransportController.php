<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\JenisTransport;

class JenisTransportController extends MasterController
{
    protected function modelClass(): string
    {
        return JenisTransport::class;
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}
