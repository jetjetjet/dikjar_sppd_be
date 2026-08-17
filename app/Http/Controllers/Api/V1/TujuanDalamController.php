<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Wilayah\Kecamatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TujuanDalamController extends MasterController
{
    protected function modelClass(): string
    {
        return Kecamatan::class;
    }

    protected function gridQuery(Request $request)
    {
        return $this->modelClass()::query()->select('id', 'name')->orderBy('name')->get();
    }

    public function search(Request $request): JsonResponse
    {
        return $this->successResponse('Ok', $this->modelClass()::all()->pluck('name'));
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }

    /**
     * "Tujuan Dalam Daerah" bukan data wilayah beneran — cuma daftar pilihan
     * kecamatan tujuan dinas, semuanya di kabupaten yang sama (Kerinci).
     * kabupaten_id NOT NULL di tabel tapi tidak pernah diisi dari form, jadi
     * di-hardcode konsisten dengan 27 baris data lama yang sudah ada.
     */
    protected function mergeAttributes(array $validated, bool $isUpdate = false): array
    {
        if (! $isUpdate) {
            $validated['kabupaten_id'] = 1501;
        }

        return $validated;
    }
}
