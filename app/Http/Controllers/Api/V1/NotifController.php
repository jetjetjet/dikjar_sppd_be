<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\NotifService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class NotifController extends Controller
{
    use ApiResponse;

    public function __construct(protected NotifService $notifService) {}

    public function notif(): JsonResponse
    {
        return $this->successResponse('Ok', $this->notifService->notif());
    }

    public function listNotif(): JsonResponse
    {
        return $this->successResponse('Ok', $this->notifService->listNotif());
    }
}
