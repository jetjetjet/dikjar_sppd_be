<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Validation\Rule;

class UpdateSPTRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'no_spt' => ['required', 'string', 'max:255', Rule::unique('spt', 'no_spt')->ignore($this->route('id'))],
            'jenis_dinas' => ['required', 'string'],
            'anggaran_id' => ['required', 'integer'],
            'pttd_id' => ['required', 'integer'],
            'pptk_id' => ['required', 'integer'],
            'dasar_pelaksana' => ['required', 'string'],
            'untuk' => ['required', 'string'],
            'transportasi' => ['required', 'string'],
            'pelaksana_id' => ['required', 'integer'],
            'tgl_berangkat' => ['required', 'date'],
            'tgl_kembali' => ['required', 'date', 'after_or_equal:tgl_berangkat'],
            'pengguna_anggaran_id' => ['required', 'integer'],
            'bendahara_id' => ['required', 'integer'],
            'tgl_spt' => ['nullable', 'date'],
            'daerah_asal' => ['nullable', 'string'],
            'daerah_tujuan' => ['nullable', 'string'],
            'pegawai_id' => ['array'],
            'pegawai_id.*' => ['integer'],
        ];
    }
}
