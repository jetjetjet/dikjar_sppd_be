<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SPTLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SPTLogController extends Controller
{
    use ApiResponse;

    public function __construct(protected SPTLogService $sptLogService) {}

    public function grid(Request $request): JsonResponse
    {
        $data = $this->sptLogService->grid($request->all());

        return $this->successResponse('Daftar log berhasil diambil', $data->items(), [
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            'last_page' => $data->lastPage(),
        ]);
    }
}
