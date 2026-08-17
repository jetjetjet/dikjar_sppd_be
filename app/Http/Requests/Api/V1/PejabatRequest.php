<?php

namespace App\Http\Requests\Api\V1;

class PejabatRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $id = $this->route('id');

        $rules = [
            'pegawai_id' => ['required', 'integer'],
            'autorisasi' => ['required', 'string'],
            'anggaran_id' => ['nullable', 'integer'],
        ];

        if (! $id) {
            $rules['is_active'] = ['required'];
        }

        return $rules;
    }
}
