<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PejabatRequest;
use App\Services\PejabatTtdService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PejabatTtdController extends Controller
{
    use ApiResponse;

    public function __construct(protected PejabatTtdService $pejabatService) {}

    public function grid(): JsonResponse
    {
        return $this->successResponse('Daftar pejabat berhasil diambil', $this->pejabatService->getGrid());
    }

    public function search(Request $request): JsonResponse
    {
        $anggaranId = $request->filled('anggaran') ? $request->integer('anggaran') : null;

        return $this->successResponse('Ok', $this->pejabatService->search($request->input('filter'), $anggaranId));
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse('Ok', $this->pejabatService->show($id));
    }

    public function store(PejabatRequest $request): JsonResponse
    {
        try {
            $this->pejabatService->store($request->all());

            return $this->successResponse('Berhasil menambahkan data baru.', null, null, 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan pejabat.', ['exception' => $e]);

            return $this->errorResponse($e->getMessage(), null, 409);
        }
    }

    public function update(PejabatRequest $request, int $id): JsonResponse
    {
        try {
            $this->pejabatService->update($id, $request->all());

            return $this->successResponse('Berhasil mengubah data.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah pejabat.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function setActive(Request $request, int $id): JsonResponse
    {
        try {
            $this->pejabatService->setActive($id, (bool) $request->input('is_active'));

            return $this->successResponse('Berhasil mengubah status aktif.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah status aktif pejabat.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->pejabatService->destroy($id);

            return $this->successResponse('Berhasil menghapus data.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus pejabat.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }
}
