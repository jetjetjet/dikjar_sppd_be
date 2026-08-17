<?php

namespace App\Http\Requests\Api\V1;

class CetakLaporanRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'hasil' => ['nullable', 'string'],
            'saran' => ['nullable', 'string'],
        ];
    }
}
