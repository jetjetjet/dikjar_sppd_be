<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PasswordRequest;
use App\Http\Requests\Api\V1\ProfileRequest;
use App\Services\ProfileService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProfileService $profileService) {}

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse('Ok', $this->profileService->show($id));
        } catch (\Exception $e) {
            Log::error('Gagal mengambil profil.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(ProfileRequest $request, int $id): JsonResponse
    {
        try {
            $this->profileService->authorizeSelf($id);
            $this->profileService->update($id, $request->all());

            return $this->successResponse('Berhasil ubah user.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah profil.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function changePassword(PasswordRequest $request, int $id): JsonResponse
    {
        try {
            $this->profileService->authorizeSelf($id);
            $this->profileService->changePassword($id, $request->input('password'));

            return $this->successResponse('Berhasil ubah password.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah password profil.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function changePhoto(Request $request, int $id): JsonResponse
    {
        try {
            $this->profileService->authorizeSelf($id);
            $path = $this->profileService->changePhoto($id, $request->file('file'));

            return $this->successResponse('Berhasil ubah poto.', ['path_foto' => $path]);
        } catch (\Exception $e) {
            Log::error('Gagal mengubah foto profil.', ['id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }
}
