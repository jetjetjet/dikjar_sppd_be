<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BiayaLockedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PengeluaranRequest;
use App\Services\PengeluaranService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengeluaranController extends Controller
{
    use ApiResponse;

    public function __construct(protected PengeluaranService $pengeluaranService) {}

    public function show(int $id): JsonResponse
    {
        $data = $this->pengeluaranService->show($id);

        return $this->successResponse('Ok', $data);
    }

    public function store(PengeluaranRequest $request): JsonResponse
    {
        try {
            $total = $this->pengeluaranService->store($request->validated());

            return $this->successResponse('Berhasil menambahkan Pengeluaran.', ['total' => $total], null, 201);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan pengeluaran.', ['exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function update(PengeluaranRequest $request, int $id): JsonResponse
    {
        try {
            $total = $this->pengeluaranService->update(
                $id,
                (int) $request->input('pegawai_id'),
                (int) $request->input('biaya_id'),
                $request->validated()
            );

            return $this->successResponse('Berhasil memperbaharui Pengeluaran.', ['total' => $total]);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal mengubah pengeluaran.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function destroy(int $id, int $biayaId, int $pegawaiId): JsonResponse
    {
        try {
            $total = $this->pengeluaranService->destroy($id, $biayaId, $pegawaiId);

            return $this->successResponse('Berhasil menghapus data Pengeluaran.', ['total' => $total]);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal menghapus pengeluaran.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat menghapus.');
        }
    }

    public function uploadFile(Request $request, int $id): JsonResponse
    {
        try {
            $this->pengeluaranService->uploadFile($id);

            return $this->successResponse('Berhasil memperbaharui data Pengeluaran.');
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal upload struk pengeluaran.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }
}
