<?php

namespace App\Http\Requests\Api\V1;

class ReportTahunanRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'jenis_dinas' => ['required', 'string'],
            'tahun_laporan' => ['required', 'numeric'],
        ];
    }
}
