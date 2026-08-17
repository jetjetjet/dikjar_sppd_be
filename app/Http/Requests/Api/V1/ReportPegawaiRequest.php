<?php

namespace App\Http\Requests\Api\V1;

class ReportPegawaiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'integer'],
            'tgl_berangkat' => ['required', 'date'],
            'tgl_kembali' => ['required', 'date'],
            'status' => ['nullable', 'string'],
        ];
    }
}
