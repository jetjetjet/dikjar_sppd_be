<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BiayaLockedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InapRequest;
use App\Services\InapService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InapController extends Controller
{
    use ApiResponse;

    public function __construct(protected InapService $inapService) {}

    public function show(int $id): JsonResponse
    {
        $data = $this->inapService->show($id);

        return $this->successResponse('Ok', $data);
    }

    public function store(InapRequest $request): JsonResponse
    {
        try {
            $total = $this->inapService->store($request->validated());

            return $this->successResponse('Berhasil menambah data Penginapan.', ['total' => $total], null, 201);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan penginapan.', ['exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function update(InapRequest $request, int $id): JsonResponse
    {
        try {
            $total = $this->inapService->update(
                $id,
                (int) $request->input('pegawai_id'),
                (int) $request->input('biaya_id'),
                $request->validated()
            );

            return $this->successResponse('Berhasil mengubah data checkin.', ['total' => $total]);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal mengubah penginapan.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }

    public function destroy(int $id, int $biayaId, int $pegawaiId): JsonResponse
    {
        try {
            $total = $this->inapService->destroy($id, $biayaId, $pegawaiId);

            return $this->successResponse('Berhasil menghapus data Penginapan.', ['total' => $total]);
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal menghapus penginapan.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat menghapus.');
        }
    }

    public function uploadFile(Request $request, int $id): JsonResponse
    {
        try {
            $this->inapService->uploadFile($id);

            return $this->successResponse('Berhasil upload struk penginapan.');
        } catch (BiayaLockedException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal upload struk penginapan.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse('Kesalahan! Tidak dapat memproses.');
        }
    }
}
