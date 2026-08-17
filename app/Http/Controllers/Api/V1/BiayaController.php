<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BiayaService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BiayaController extends Controller
{
    use ApiResponse;

    public function __construct(protected BiayaService $biayaService) {}

    public function grid(int $biayaId, int $pegawaiId): JsonResponse
    {
        $data = $this->biayaService->getGrid($biayaId, $pegawaiId);

        return $this->successResponse('Daftar biaya berhasil diambil', $data);
    }
}
