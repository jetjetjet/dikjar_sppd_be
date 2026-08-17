<?php

namespace App\Http\Requests\Api\V1;

class PegawaiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nip' => ['nullable', 'string'],
            'full_name' => ['required', 'string'],
            'jabatan' => ['required', 'string'],
            'pangkat' => ['nullable', 'string'],
            'golongan' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'jenis_kelamin' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string'],
            'tgl_lahir' => ['nullable'],
            'status_pegawai' => ['required', 'in:aktif,pensiun'],
        ];
    }
}
