<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CetakLaporanRequest;
use App\Services\SPPDService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SPPDController extends Controller
{
    use ApiResponse;

    public function __construct(protected SPPDService $sppdService) {}

    public function grid(int $id): JsonResponse
    {
        $data = $this->sppdService->getGrid($id);

        return $this->successResponse('Daftar SPPD berhasil diambil', $data);
    }

    public function show(int $id, int $sptDetailId, int $pegawaiId): JsonResponse
    {
        $data = $this->sppdService->getDetail($id, $sptDetailId, $pegawaiId);

        if ($data === null) {
            return $this->errorResponse('Data SPPD tidak ditemukan', null, 404);
        }

        return $this->successResponse('Detail SPPD berhasil diambil', $data);
    }

    public function cetakSPPD(int $id): JsonResponse
    {
        $path = $this->sppdService->getSPPDFile($id);

        if ($path === null) {
            return $this->errorResponse('File SPPD tidak ditemukan.', null, 404);
        }

        return $this->successResponse('Ok', $path);
    }

    public function getSPPD(int $id, int $pegawaiId): JsonResponse
    {
        $path = $this->sppdService->getSPPDDocument($id, $pegawaiId);

        if ($path === null) {
            return $this->errorResponse('SPPD tidak ditemukan!', null, 404);
        }

        return $this->successResponse('Ok', $path);
    }

    public function cetakRumming(int $id, int $biayaId, int $pegawaiId): JsonResponse
    {
        try {
            $path = $this->sppdService->cetakRumming($id, $biayaId, $pegawaiId);

            return $this->successResponse('Berhasil mencetak rumming.', $path);
        } catch (\Exception $e) {
            Log::error('Gagal mencetak rumming.', ['id' => $id, 'biaya_id' => $biayaId, 'pegawai_id' => $pegawaiId, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function cetakKwitansi(int $id): JsonResponse
    {
        try {
            $path = $this->sppdService->cetakKwitansi($id);

            return $this->successResponse('Berhasil mencetak kwitansi.', $path);
        } catch (\Exception $e) {
            Log::error('Gagal mencetak kwitansi.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function cetakLaporan(CetakLaporanRequest $request, int $id): JsonResponse
    {
        try {
            $path = $this->sppdService->cetakLaporan($id, $request->input('hasil'), $request->input('saran'));

            return $this->successResponse('OK.', $path);
        } catch (\Exception $e) {
            Log::error('Gagal mencetak laporan.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }
}
