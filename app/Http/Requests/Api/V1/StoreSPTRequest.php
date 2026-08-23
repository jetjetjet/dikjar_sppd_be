<?php

namespace App\Http\Requests\Api\V1;

class StoreSPTRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'anggaran_id' => ['required', 'integer'],
            'jenis_dinas' => ['required', 'string'],
            'pttd_id' => ['required', 'integer'],
            'pptk_id' => ['required', 'integer'],
            'bendahara_id' => ['required', 'integer'],
            'tgl_spt' => ['required', 'date'],
            'pelaksana_id' => ['required', 'integer'],
            'dasar_pelaksana' => ['required', 'string'],
            'untuk' => ['required', 'string'],
            'transportasi' => ['required', 'string'],
            'tgl_berangkat' => ['required', 'date'],
            'tgl_kembali' => ['required', 'date', 'after_or_equal:tgl_berangkat'],
            'daerah_asal' => ['required', 'string'],
            'daerah_tujuan' => ['required', 'string'],
            'pengguna_anggaran_id' => ['required', 'integer'],
            'pegawai_id' => ['array'],
            'pegawai_id.*' => ['integer'],
        ];
    }
}
