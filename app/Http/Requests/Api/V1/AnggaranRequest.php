<?php

namespace App\Http\Requests\Api\V1;

class AnggaranRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'kode_rekening' => ['required', 'string'],
            'nama_rekening' => ['required', 'string'],
            'bidang' => ['required', 'string'],
            'uraian' => ['nullable', 'string'],
            'pagu' => ['required', 'numeric'],
            'periode' => ['required', 'integer'],
            'bendahara_id' => ['nullable', 'integer'],
            'pptk_id' => ['nullable', 'integer'],
            'pengguna_id' => ['nullable', 'integer'],
        ];
    }
}
