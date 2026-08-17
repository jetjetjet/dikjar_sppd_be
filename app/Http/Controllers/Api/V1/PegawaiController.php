<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PegawaiRequest;
use App\Services\PegawaiService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PegawaiController extends Controller
{
    use ApiResponse;

    public function __construct(protected PegawaiService $pegawaiService) {}

    public function grid(): JsonResponse
    {
        $data = $this->pegawaiService->getGrid();

        return $this->successResponse('Daftar pegawai berhasil diambil', $data);
    }

    public function search(Request $request): JsonResponse
    {
        $data = $this->pegawaiService->search($request->all(), $request->integer('id'));

        return $this->successResponse('Ok', $data);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->pegawaiService->show($id);

        return $this->successResponse('Ok', $data);
    }

    public function store(PegawaiRequest $request): JsonResponse
    {
        try {
            $this->pegawaiService->store($request->validated());

            return $this->successResponse('Berhasil menambahkan pegawai baru.', null, null, 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan pegawai.', ['exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(PegawaiRequest $request, int $id): JsonResponse
    {
        try {
            $this->pegawaiService->update($id, $request->validated());

            return $this->successResponse('Berhasil ubah pegawai.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah pegawai.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function changePhoto(Request $request, int $id): JsonResponse
    {
        try {
            $path = $this->pegawaiService->changePhoto($id);

            return $this->successResponse('Berhasil ubah poto.', ['path_foto' => $path]);
        } catch (\Exception $e) {
            Log::error('Gagal mengubah foto pegawai.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->pegawaiService->destroy($id);

            return $this->successResponse('Berhasil menghapus pegawai.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus pegawai.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }
}
