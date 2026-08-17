<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(protected DashboardService $dashboardService) {}

    public function anggaran(): JsonResponse
    {
        return $this->successResponse('Ok', $this->dashboardService->anggaran());
    }

    public function pegawaiDinas(): JsonResponse
    {
        return $this->successResponse('Ok', $this->dashboardService->pegawaiDinas());
    }
}
