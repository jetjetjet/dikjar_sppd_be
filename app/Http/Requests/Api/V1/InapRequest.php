<?php

namespace App\Http\Requests\Api\V1;

class InapRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'integer'],
            'biaya_id' => ['required', 'integer'],
            'hotel' => ['required', 'string'],
            'room' => ['required', 'string'],
            'harga' => ['nullable', 'numeric'],
            'tgl_checkin' => ['required', 'date'],
            'tgl_checkout' => ['nullable', 'date', 'after_or_equal:tgl_checkin'],
            'jml_hari' => ['nullable', 'integer'],
            'total_bayar' => ['nullable', 'numeric'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}
