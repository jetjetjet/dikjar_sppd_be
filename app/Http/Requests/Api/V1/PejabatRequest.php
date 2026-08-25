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
            // Template Word per pejabat (khusus PTTD) — opsional, lihat PLAN_TEMPLATE_PER_PEJABAT.md.
            // Ukuran maks disamakan dengan client_max_body_size Nginx backend (DEPLOYMENT.md Bagian 11.2).
            'template' => ['nullable', 'file', 'mimes:docx', 'max:20480'],
        ];

        if (! $id) {
            $rules['is_active'] = ['required'];
        }

        return $rules;
    }
}
