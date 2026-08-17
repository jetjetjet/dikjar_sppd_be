<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VerifikasiRequest;
use App\Services\CekService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CekSPTController extends Controller
{
    use ApiResponse;

    public function __construct(protected CekService $cekService) {}

    public function verifikasi(VerifikasiRequest $request): JsonResponse
    {
        $result = $this->cekService->verifikasi($request->input('key'));

        if ($result['success']) {
            return $this->successResponse('SPT ditemukan!', $result['data']);
        }

        return $this->successResponse('SPT tidak ditemukan!', $result['data']);
    }
}
