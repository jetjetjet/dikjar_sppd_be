<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSPTRequest;
use App\Http\Requests\Api\V1\UpdateSPTRequest;
use App\Http\Requests\Api\V1\VoidSPTRequest;
use App\Services\SPTService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SPTController extends Controller
{
    use ApiResponse;

    public function __construct(protected SPTService $sptService) {}

    public function grid(Request $request): JsonResponse
    {
        $data = $this->sptService->getGrid($request->input('tahun'));

        return $this->successResponse('Daftar SPT berhasil diambil', $data);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->sptService->getDetail($id);

        if ($data === null) {
            return $this->errorResponse('Data SPT tidak ditemukan', null, 404);
        }

        return $this->successResponse('Detail SPT berhasil diambil', $data);
    }

    public function store(StoreSPTRequest $request): JsonResponse
    {
        try {
            $this->sptService->store($request->validated());

            return $this->successResponse('Berhasil menambahkan SPT baru.', null, null, 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan SPT baru.', ['exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function update(UpdateSPTRequest $request, int $id): JsonResponse
    {
        try {
            $this->sptService->update($id, $request->validated());

            return $this->successResponse('Berhasil mengubah SPT.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah SPT.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->sptService->delete($id);

            return $this->successResponse('Berhasil menghapus data.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus SPT.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat menghapus.');
        }
    }

    public function proses(int $id): JsonResponse
    {
        try {
            $path = $this->sptService->proses($id);

            return $this->successResponse('Berhasil memproses SPT.', $path);
        } catch (\Exception $e) {
            Log::error('Gagal memproses SPT.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function finish(int $id): JsonResponse
    {
        try {
            $this->sptService->finish($id);

            return $this->successResponse('Perjalanan Dinas berhasil diselesaikan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyelesaikan SPT.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function void(VoidSPTRequest $request, int $id): JsonResponse
    {
        try {
            $this->sptService->void($id, $request->input('void_remark'));

            return $this->successResponse('Berhasil mengubah status SPT menjadi VOID.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah status SPT menjadi VOID.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('SPT tidak dapat diubah menjadi VOID.');
        }
    }

    public function cetakSpt(int $id): JsonResponse
    {
        $path = $this->sptService->getSPTFile($id);

        if ($path === null) {
            return $this->errorResponse('SPT file tidak ditemukan.', null, 404);
        }

        return $this->successResponse('Ok', $path);
    }

    public function getSPT(int $id): JsonResponse
    {
        $path = $this->sptService->getSPTDocument($id);

        if ($path === null) {
            return $this->errorResponse('SPT tidak ditemukan!', null, 404);
        }

        return $this->successResponse('Ok', $path);
    }
}
