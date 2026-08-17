<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Jabatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JabatanController extends MasterController
{
    protected function modelClass(): string
    {
        return Jabatan::class;
    }

    protected function gridQuery(Request $request)
    {
        return $this->modelClass()::query()->orderByDesc('created_at')->get();
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [
            'name' => ['required', 'string'],
            'remark' => ['nullable', 'string'],
            'is_parent' => ['nullable', 'boolean'],
            'parent_id' => ['nullable', 'integer'],
        ];
    }

    public function parent(): JsonResponse
    {
        return $this->successResponse('Ok', $this->modelClass()::where('is_parent', '1')->get());
    }
}
