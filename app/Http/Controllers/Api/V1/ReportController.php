<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReportPegawaiRequest;
use App\Http\Requests\Api\V1\ReportTahunanRequest;
use App\Services\ReportService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    use ApiResponse;

    public function __construct(protected ReportService $reportService) {}

    public function reportByFinishedSPT(ReportTahunanRequest $request): JsonResponse
    {
        $data = $this->reportService->reportByFinishedSPT($request->input('jenis_dinas'), (int) $request->input('tahun_laporan'));

        $message = $data->count() > 0 ? 'Laporan ditemukan.' : 'Laporan tidak ditemukan.';

        return $this->successResponse($message, $data);
    }

    public function reportByPegawai(ReportPegawaiRequest $request): JsonResponse
    {
        $data = $this->reportService->reportByPegawai($request->all());

        return $this->successResponse($data->count() > 0 ? 'Laporan ditemukan.' : 'Laporan tidak ditemukan!', $data);
    }
}
