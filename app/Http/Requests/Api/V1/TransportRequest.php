<?php

namespace App\Http\Requests\Api\V1;

class TransportRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'integer'],
            'biaya_id' => ['required', 'integer'],
            'jenis_transport' => ['required', 'string'],
            'perjalanan' => ['required', 'string'],
            'agen' => ['required', 'string'],
            'no_tiket' => ['required', 'string'],
            'kode_booking' => ['nullable', 'string'],
            'no_penerbangan' => ['nullable', 'string'],
            'tgl' => ['required', 'date'],
            'total_bayar' => ['nullable', 'numeric'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}
