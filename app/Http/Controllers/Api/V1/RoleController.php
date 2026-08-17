<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RoleRequest;
use App\Services\RoleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(protected RoleService $roleService) {}

    public function grid(): JsonResponse
    {
        return $this->successResponse('Daftar role berhasil diambil', $this->roleService->getGrid());
    }

    public function getPermission(): JsonResponse
    {
        return $this->successResponse('Ok', $this->roleService->getPermission());
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse('Ok', $this->roleService->show($id));
    }

    public function store(RoleRequest $request): JsonResponse
    {
        try {
            $perms = json_decode($request->input('perms', '[]'), true) ?? [];
            $this->roleService->store($request->input('name'), $this->roleService->selectedPerms($perms));

            return $this->successResponse('Berhasil menambahkan peran baru.', null, null, 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan peran.', ['exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(RoleRequest $request, int $id): JsonResponse
    {
        try {
            $perms = json_decode($request->input('perms', '[]'), true) ?? [];
            $this->roleService->update($id, $request->input('name'), $this->roleService->selectedPerms($perms));

            return $this->successResponse('Berhasil mengubah peran.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah peran.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->roleService->destroy($id);

            return $this->successResponse('Berhasil menghapus peran.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus peran.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }
}
