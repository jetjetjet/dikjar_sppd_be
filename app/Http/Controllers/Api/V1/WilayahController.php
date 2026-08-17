<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wilayah\Desa;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Provinsi;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    use ApiResponse;

    public function getProvinsi(Request $request): JsonResponse
    {
        $cari = $request->input('q', '');

        $data = Provinsi::whereRaw('UPPER(name) LIKE UPPER(?)', ['%'.$cari.'%'])
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return $this->successResponse('Ok', $data);
    }

    public function getKabupaten(Request $request): JsonResponse
    {
        $cari = $request->input('q', '');

        $q = Kabupaten::whereRaw('UPPER(name) LIKE UPPER(?)', ['%'.$cari.'%'])
            ->whereNotIn('id', [1501]);

        if ($request->has('provinsi_id')) {
            $q->where('provinsi_id', $request->integer('provinsi_id'));
        }

        $data = $q->select('id', 'name')->orderBy('name')->limit(10)->get();

        return $this->successResponse('Ok', $data);
    }

    public function getKecamatan(Request $request): JsonResponse
    {
        $cari = $request->input('q', '');
        $kotaId = 1501;

        $data = Kecamatan::where('kabupaten_id', $kotaId)
            ->whereRaw('UPPER(name) LIKE UPPER(?)', ['%'.$cari.'%'])
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return $this->successResponse('Ok', $data);
    }

    public function getDesa(Request $request): JsonResponse
    {
        $request->validate(['kecamatan_id' => 'required|integer']);
        $cari = $request->input('q', '');

        $data = Desa::where('kecamatan_id', $request->integer('kecamatan_id'))
            ->whereRaw('UPPER(name) LIKE UPPER(?)', ['%'.$cari.'%'])
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return $this->successResponse('Ok', $data);
    }
}
