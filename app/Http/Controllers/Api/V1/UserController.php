<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PasswordRequest;
use App\Http\Requests\Api\V1\UserRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(protected UserService $userService) {}

    public function grid(): JsonResponse
    {
        return $this->successResponse('Daftar user berhasil diambil', $this->userService->getGrid());
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse('Ok', $this->userService->show($id));
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $this->userService->store($request->all());

            return $this->successResponse('Berhasil menambahkan user baru.', null, null, 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan user.', ['exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UserRequest $request, int $id): JsonResponse
    {
        try {
            $this->userService->update($id, $request->all());

            return $this->successResponse('Berhasil ubah User.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah user.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function changePassword(PasswordRequest $request, int $id): JsonResponse
    {
        try {
            $this->userService->changePassword($id, $request->input('password'));

            return $this->successResponse('Berhasil ubah password.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah password user.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->destroy($id);

            return $this->successResponse('Berhasil menghapus user.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus user.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }
}
