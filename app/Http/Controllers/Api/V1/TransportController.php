<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BiayaLockedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TransportRequest;
use App\Services\TransportService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransportController extends Controller
{
    use ApiResponse;

    public function __construct(protected TransportService $transportService) {}

    public function show(int $id): JsonResponse
    {
        $data = $this->transportService->show($id);

        return $this->successResponse('Ok', $data);
    }

    public function store(TransportRequest $request): JsonResponse
    {
        try {
            $total = $this->transportService->store($request->validated());

            return $this->successResponse('Berhasil menambahkan Perjalanan.', ['total' => $total], null, 201);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan transportasi.', ['exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function update(TransportRequest $request, int $id): JsonResponse
    {
        try {
            $total = $this->transportService->update(
                $id,
                (int) $request->input('pegawai_id'),
                (int) $request->input('biaya_id'),
                $request->validated()
            );

            return $this->successResponse('Berhasil memperbaharui transportasi.', ['total' => $total]);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal mengubah transportasi.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function destroy(int $id, int $biayaId, int $pegawaiId): JsonResponse
    {
        try {
            $total = $this->transportService->destroy($id, $biayaId, $pegawaiId);

            return $this->successResponse('Berhasil menghapus data transportasi.', ['total' => $total]);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal menghapus transportasi.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat menghapus.');
        }
    }

    public function uploadFile(Request $request, int $id): JsonResponse
    {
        try {
            $this->transportService->uploadFile($id);

            return $this->successResponse('Berhasil upload struk transportasi.');
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal upload struk transportasi.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }
}
