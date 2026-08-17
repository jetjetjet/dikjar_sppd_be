<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AnggaranRequest;
use App\Services\AnggaranService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AnggaranController extends Controller
{
    use ApiResponse;

    public function __construct(protected AnggaranService $anggaranService) {}

    public function grid(): JsonResponse
    {
        try {
            $data = $this->anggaranService->grid();

            return $this->successResponse('Daftar anggaran berhasil diambil', $data);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil daftar anggaran.', ['exception' => $e]);

            return $this->errorResponse($e->getMessage(), null, 500);
        }
    }

    public function searchRole(): JsonResponse
    {
        return $this->successResponse('Ok', $this->anggaranService->searchRole());
    }

    public function search(): JsonResponse
    {
        try {
            $data = $this->anggaranService->search();

            return $this->successResponse('Ok', $data);
        } catch (\Exception $e) {
            Log::error('Gagal mencari anggaran.', ['exception' => $e]);

            return $this->errorResponse($e->getMessage(), null, 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->anggaranService->show($id);

        return $this->successResponse('Ok', $data);
    }

    public function store(AnggaranRequest $request): JsonResponse
    {
        try {
            $this->anggaranService->store($request->validated());

            return $this->successResponse('Berhasil menambahkan Anggaran baru.', null, null, 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan anggaran.', ['exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function update(AnggaranRequest $request, int $id): JsonResponse
    {
        try {
            $this->anggaranService->update($id, $request->validated());

            return $this->successResponse('Berhasil mengubah Anggaran.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah anggaran.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->anggaranService->destroy($id);

            return $this->successResponse('Berhasil menghapus anggaran.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus anggaran.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }
}
