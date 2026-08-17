<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Thin generic master-data CRUD controller.
 * Subclasses define the model class, search field and validation rules.
 */
abstract class MasterController extends Controller
{
    use ApiResponse;

    abstract protected function modelClass(): string;

    protected function fields(): array
    {
        return ['*'];
    }

    protected function searchField(): string
    {
        return 'name';
    }

    protected function validationRules(bool $isUpdate = false): array
    {
        return [];
    }

    /**
     * Hook for subclasses to inject attributes not collected from the client
     * (e.g. a fixed foreign key) before create/update.
     */
    protected function mergeAttributes(array $validated, bool $isUpdate = false): array
    {
        return $validated;
    }

    protected function gridQuery(Request $request)
    {
        return $this->modelClass()::query()->get($this->fields());
    }

    public function grid(Request $request): JsonResponse
    {
        return $this->successResponse('Daftar berhasil diambil', $this->gridQuery($request));
    }

    public function search(Request $request): JsonResponse
    {
        $label = $this->searchField();

        return $this->successResponse('Ok', $this->modelClass()::select('id', "$label as label")->get());
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse('Ok', $this->modelClass()::find($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->mergeAttributes($request->validate($this->validationRules(false)));

        try {
            $this->modelClass()::create($validated);

            return $this->successResponse('Berhasil menambahkan data baru.', null, null, 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan data master.', ['model' => $this->modelClass(), 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $this->mergeAttributes($request->validate($this->validationRules(true)), true);

        try {
            $this->modelClass()::findOrFail($id)->update($validated);

            return $this->successResponse('Berhasil mengubah data.');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah data master.', ['model' => $this->modelClass(), 'id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->modelClass()::findOrFail($id)->delete();

            return $this->successResponse('Berhasil menghapus data.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus data master.', ['model' => $this->modelClass(), 'id' => $id, 'exception' => $e]);

            return $this->errorResponse($e->getMessage());
        }
    }
}
