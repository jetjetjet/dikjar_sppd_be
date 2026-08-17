<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->input('email'), $request->input('password'));

            return $this->successResponse('Login berhasil', $data);
        } catch (\Exception $e) {
            Log::warning('Login gagal.', ['email' => $request->input('email'), 'message' => $e->getMessage()]);

            return $this->errorResponse($e->getMessage(), null, 401);
        }
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse('Logout berhasil', null);
    }
}
