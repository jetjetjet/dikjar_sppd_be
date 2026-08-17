<?php

namespace App\Http\Requests\Api\V1;

class VerifikasiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'key' => ['required', 'string'],
        ];
    }
}
