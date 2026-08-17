<?php

namespace App\Http\Requests\Api\V1;

class PengeluaranRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'integer'],
            'biaya_id' => ['required', 'integer'],
            'tgl' => ['required', 'date'],
            'kategori' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
            'nominal' => ['required', 'numeric'],
            'satuan' => ['required', 'string'],
            'jml' => ['required', 'integer'],
            'jml_hari' => ['nullable', 'integer'],
            'total' => ['required', 'numeric'],
        ];
    }
}
